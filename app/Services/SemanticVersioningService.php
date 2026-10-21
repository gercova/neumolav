<?php

namespace App\Services;

use App\Models\AppVersion;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class SemanticVersioningService
{
    /**
     * Get the current active version of the application.
     */
    public function getCurrentVersion(): string
    {
        return AppVersion::latestVersion();
    }

    /**
     * Parse semantic version string into components.
     *
     * @return array{major: int, minor: int, patch: int}
     */
    public function parseSemVer(string $version): array
    {
        $clean = ltrim(trim($version), 'vV');
        $parts = explode('.', $clean);

        return [
            'major' => isset($parts[0]) && is_numeric($parts[0]) ? (int) $parts[0] : 1,
            'minor' => isset($parts[1]) && is_numeric($parts[1]) ? (int) $parts[1] : 0,
            'patch' => isset($parts[2]) && is_numeric($parts[2]) ? (int) $parts[2] : 0,
        ];
    }

    /**
     * Compute next version given a base version and bump type.
     */
    public function calculateNextVersion(string $currentVersion, string $type): string
    {
        $semver = $this->parseSemVer($currentVersion);
        $type = strtoupper(trim($type));

        switch ($type) {
            case 'MAJOR':
                return sprintf('%d.0.0', $semver['major'] + 1);
            case 'MINOR':
                return sprintf('%d.%d.0', $semver['major'], $semver['minor'] + 1);
            case 'PATCH':
            default:
                return sprintf('%d.%d.%d', $semver['major'], $semver['minor'], $semver['patch'] + 1);
        }
    }

    /**
     * Detect changes in the application since the last recorded version or in current git status.
     *
     * @return array<string, mixed>
     */
    public function detectPendingChanges(): array
    {
        $currentVersion = $this->getCurrentVersion();
        $latestRecord = AppVersion::orderByDesc('id')->first();
        $baseCommit = $latestRecord?->commit_hash;

        $gitStatusFiles = $this->getGitStatusFiles();
        $isCleanWorkingTree = empty($gitStatusFiles);

        // If working tree is clean and we have a previous commit, diff against that commit; otherwise diff HEAD~1
        $diffTarget = null;
        if ($isCleanWorkingTree) {
            if ($baseCommit && $this->isValidGitCommit($baseCommit)) {
                $diffTarget = "{$baseCommit}..HEAD";
            } else {
                $diffTarget = 'HEAD~1..HEAD';
            }
        }

        $changedFiles = ! empty($gitStatusFiles) ? $gitStatusFiles : $this->getGitDiffFiles($diffTarget);

        // Fallback: If git has no diff at all, inspect recent file modifications
        if (empty($changedFiles)) {
            $changedFiles = $this->getRecentlyModifiedFiles();
        }

        $classesChanged = [];
        $methodsChanged = [];
        $viewsChanged = [];
        $scriptsChanged = [];
        $dependenciesChanged = [];
        $updates = [];
        $breakingChanges = 0;
        $minorAdditions = 0;
        $patchChanges = 0;

        foreach ($changedFiles as $fileInfo) {
            $path = $fileInfo['path'];
            $status = $fileInfo['status']; // added, modified, deleted

            // 1. PHP Classes and Methods in app/
            if (str_starts_with($path, 'app/') && str_ends_with($path, '.php')) {
                $classAnalysis = $this->analyzePhpClassAndMethods($path, $status);
                if ($classAnalysis) {
                    $classesChanged[] = $classAnalysis['class_info'];
                    foreach ($classAnalysis['methods'] as $method) {
                        $methodsChanged[] = $method;
                        if (! empty($method['is_breaking'])) {
                            $breakingChanges++;
                        } elseif ($method['change_type'] === 'added' && $method['visibility'] === 'public') {
                            $minorAdditions++;
                        } else {
                            $patchChanges++;
                        }
                    }
                }
            }

            // 2. Views in resources/views/
            elseif (str_starts_with($path, 'resources/views/') && str_ends_with($path, '.blade.php')) {
                $viewName = str_replace(['resources/views/', '.blade.php'], '', $path);
                $viewsChanged[] = [
                    'file' => $path,
                    'name' => $viewName,
                    'change_type' => $status,
                ];

                if ($status === 'added') {
                    $minorAdditions++;
                } else {
                    $patchChanges++;
                }
            }

            // 3. Scripts and Frontend assets in resources/js/ and resources/css/
            elseif ((str_starts_with($path, 'resources/js/') || str_starts_with($path, 'resources/css/')) &&
                (str_ends_with($path, '.js') || str_ends_with($path, '.vue') || str_ends_with($path, '.css'))) {
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $scriptsChanged[] = [
                    'file' => $path,
                    'type' => $ext,
                    'change_type' => $status,
                ];

                if ($status === 'added') {
                    $minorAdditions++;
                } else {
                    $patchChanges++;
                }
            }

            // 4. Dependencies
            elseif (in_array($path, ['composer.json', 'composer.lock', 'package.json', 'package-lock.json'], true)) {
                $depAnalysis = $this->analyzeDependencies($path);
                foreach ($depAnalysis as $dep) {
                    $dependenciesChanged[] = $dep;
                    if (! empty($dep['is_major'])) {
                        $breakingChanges++;
                    } elseif ($dep['change_type'] === 'added') {
                        $minorAdditions++;
                    } else {
                        $patchChanges++;
                    }
                }
            }

            // 5. System Updates (Migrations, routes, config)
            elseif (str_starts_with($path, 'database/migrations/') ||
                str_starts_with($path, 'routes/') ||
                str_starts_with($path, 'config/')) {
                $updateType = 'config';
                if (str_starts_with($path, 'database/migrations/')) {
                    $updateType = 'migration';
                } elseif (str_starts_with($path, 'routes/')) {
                    $updateType = 'route';
                }

                $updates[] = [
                    'file' => $path,
                    'type' => $updateType,
                    'change_type' => $status,
                ];

                if ($status === 'added' || $updateType === 'migration') {
                    $minorAdditions++;
                } else {
                    $patchChanges++;
                }
            }
        }

        // Determine recommended SemVer
        $recommendedType = 'PATCH';
        $rationale = 'Cambios internos, optimizaciones o correcciones menores.';

        if ($breakingChanges > 0) {
            $recommendedType = 'MAJOR';
            $rationale = "Se detectaron {$breakingChanges} cambios incompatibles (métodos eliminados, parámetros obligatorios alterados o dependencias mayores).";
        } elseif ($minorAdditions > 0 || count($classesChanged) > 0 || count($viewsChanged) > 0) {
            $recommendedType = 'MINOR';
            $rationale = 'Se incorporaron nuevas funcionalidades, clases, métodos públicos, vistas o módulos compatibles hacia atrás.';
        } elseif (count($changedFiles) === 0) {
            $rationale = 'El árbol de trabajo está sincronizado con la última versión registrada.';
        }

        $nextVersion = $this->calculateNextVersion($currentVersion, $recommendedType);

        // Stats summary
        $stats = [
            'total_files_changed' => count($changedFiles),
            'classes_count' => count($classesChanged),
            'methods_count' => count($methodsChanged),
            'views_count' => count($viewsChanged),
            'scripts_count' => count($scriptsChanged),
            'dependencies_count' => count($dependenciesChanged),
            'updates_count' => count($updates),
            'breaking_count' => $breakingChanges,
        ];

        // Automatic title summary
        $autoSummary = $this->generateAutoSummary($stats, $recommendedType);

        return [
            'current_version' => $currentVersion,
            'next_version' => $nextVersion,
            'recommended_type' => $recommendedType,
            'rationale' => $rationale,
            'auto_summary' => $autoSummary,
            'commit_hash' => $this->getCurrentCommitHash(),
            'classes_changed' => $classesChanged,
            'methods_changed' => $methodsChanged,
            'views_changed' => $viewsChanged,
            'scripts_changed' => $scriptsChanged,
            'dependencies_changed' => $dependenciesChanged,
            'updates' => $updates,
            'stats' => $stats,
            'has_changes' => count($changedFiles) > 0,
        ];
    }

    /**
     * Record a new version snapshot into the database.
     *
     * @param  array<string, mixed>  $data
     */
    public function recordVersion(array $data, ?int $userId = null): AppVersion
    {
        $version = trim($data['version']);
        $versionType = strtoupper(trim($data['version_type'] ?? 'PATCH'));
        $summary = trim($data['summary'] ?? "Versión {$version}");
        $description = $data['description'] ?? null;

        $commitHash = $data['commit_hash'] ?? $this->getCurrentCommitHash();
        $snapshotHash = hash('sha256', $version.time().($commitHash ?: 'local'));

        return AppVersion::create([
            'version' => $version,
            'version_type' => $versionType,
            'summary' => $summary,
            'description' => $description,
            'commit_hash' => $commitHash,
            'classes_changed' => $data['classes_changed'] ?? [],
            'methods_changed' => $data['methods_changed'] ?? [],
            'views_changed' => $data['views_changed'] ?? [],
            'scripts_changed' => $data['scripts_changed'] ?? [],
            'dependencies_changed' => $data['dependencies_changed'] ?? [],
            'updates' => $data['updates'] ?? [],
            'stats' => $data['stats'] ?? [],
            'snapshot_hash' => $snapshotHash,
            'created_by' => $userId,
        ]);
    }

    /**
     * Inspect a PHP file to extract class information, methods, and parameters.
     *
     * @return array{class_info: array<string, mixed>, methods: array<int, array<string, mixed>>}|null
     */
    public function analyzePhpClassAndMethods(string $relativePath, string $status): ?array
    {
        $fullPath = base_path($relativePath);
        if (! File::exists($fullPath) && $status !== 'deleted') {
            return null;
        }

        $code = File::exists($fullPath) ? File::get($fullPath) : '';
        $className = $this->extractClassNameFromFile($relativePath, $code);

        $methods = [];
        if ($status !== 'deleted' && ! empty($code)) {
            $methods = $this->extractMethodsAndParameters($className, $code, $relativePath);
        } else {
            $methods[] = [
                'class' => $className,
                'method' => '*',
                'visibility' => 'public',
                'change_type' => 'deleted',
                'is_breaking' => true,
                'description' => "Clase {$className} eliminada.",
                'parameters' => [],
            ];
        }

        $classInfo = [
            'file' => $relativePath,
            'class_name' => $className,
            'change_type' => $status,
            'methods_count' => count($methods),
        ];

        return [
            'class_info' => $classInfo,
            'methods' => $methods,
        ];
    }

    /**
     * Extract class name including namespace from file path and code.
     */
    protected function extractClassNameFromFile(string $relativePath, string $code): string
    {
        if (preg_match('/namespace\s+([^;]+);/i', $code, $matches)) {
            $namespace = trim($matches[1]);
            $className = pathinfo($relativePath, PATHINFO_FILENAME);

            return "{$namespace}\\{$className}";
        }

        // Derive from path e.g. app/Http/Controllers/security/ConfigController.php -> App\Http\Controllers\security\ConfigController
        $clean = preg_replace('/^app\//', 'App/', $relativePath);
        $clean = preg_replace('/\.php$/', '', $clean);

        return str_replace('/', '\\', $clean);
    }

    /**
     * Extract methods and parameters with their types, defaults, and optional status.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function extractMethodsAndParameters(string $className, string $code, string $relativePath): array
    {
        $methods = [];

        // Regex to match PHP methods:
        // (public|protected|private)?\s*(static\s+)?function\s+([a-zA-Z0-9_]+)\s*\(([^)]*)\)\s*(:\s*([a-zA-Z0-9_?\\|]+))?
        $pattern = '/(?:(public|protected|private)\s+)?(?:(static)\s+)?function\s+([a-zA-Z0-9_]+)\s*\((.*?)\)(?:\s*:\s*([a-zA-Z0-9_?\\|]+))?/is';

        if (preg_match_all($pattern, $code, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $visibility = ! empty($match[1]) ? strtolower(trim($match[1])) : 'public';
                $isStatic = ! empty($match[2]);
                $methodName = trim($match[3]);
                $paramsString = trim($match[4]);
                $returnType = ! empty($match[5]) ? trim($match[5]) : null;

                $parameters = $this->parseParametersString($paramsString);

                // Check git diff for this method to see if parameters or body changed
                $methodDiffInfo = $this->inspectMethodGitDiff($relativePath, $methodName);

                $methods[] = [
                    'class' => $className,
                    'method' => $methodName,
                    'visibility' => $visibility,
                    'is_static' => $isStatic,
                    'return_type' => $returnType,
                    'change_type' => $methodDiffInfo['change_type'],
                    'is_breaking' => $methodDiffInfo['is_breaking'],
                    'description' => $methodDiffInfo['description'],
                    'parameters' => $parameters,
                    'parameters_count' => count($parameters),
                ];
            }
        }

        return $methods;
    }

    /**
     * Parse parameter string like: "User $user, ?string $path = null, int $count = 0"
     *
     * @return array<int, array<string, mixed>>
     */
    protected function parseParametersString(string $paramsString): array
    {
        if (trim($paramsString) === '') {
            return [];
        }

        $params = [];
        $tokens = explode(',', $paramsString);

        foreach ($tokens as $token) {
            $token = trim($token);
            if ($token === '') {
                continue;
            }

            $type = null;
            $name = '';
            $default = null;
            $isOptional = false;

            // Check if there is a default value: $param = default
            if (str_contains($token, '=')) {
                $parts = explode('=', $token, 2);
                $left = trim($parts[0]);
                $default = trim($parts[1]);
                $isOptional = true;
            } else {
                $left = $token;
            }

            // Split into type and variable name ($name)
            if (preg_match('/^(.*?)\s*(&?\$[a-zA-Z0-9_]+)$/', $left, $typeMatches)) {
                $type = trim($typeMatches[1]) ?: null;
                $name = trim($typeMatches[2]);
            } else {
                $name = $left;
            }

            $params[] = [
                'name' => $name,
                'type' => $type,
                'is_optional' => $isOptional,
                'default' => $default,
            ];
        }

        return $params;
    }

    /**
     * Inspect Git diff for a specific method in a file.
     *
     * @return array{change_type: string, is_breaking: bool, description: string}
     */
    protected function inspectMethodGitDiff(string $relativePath, string $methodName): array
    {
        $diff = $this->runGitCommand(['diff', 'HEAD', '--', $relativePath]);

        if (empty($diff)) {
            // Check unstaged or staged diff
            $diff = $this->runGitCommand(['diff', '--staged', '--', $relativePath]);
        }

        if (empty($diff)) {
            return [
                'change_type' => 'modified',
                'is_breaking' => false,
                'description' => "Método {$methodName} activo.",
            ];
        }

        // Check if method signature appears in addition lines
        $escapedMethod = preg_quote($methodName, '/');
        $hasAddedSignature = (bool) preg_match('/^\+\s*(public|protected|private)?\s*function\s+'.$escapedMethod.'\s*\(/m', $diff);
        $hasRemovedSignature = (bool) preg_match('/^-\s*(public|protected|private)?\s*function\s+'.$escapedMethod.'\s*\(/m', $diff);

        if ($hasAddedSignature && ! $hasRemovedSignature) {
            return [
                'change_type' => 'added',
                'is_breaking' => false,
                'description' => "Nuevo método {$methodName} incorporado.",
            ];
        }

        if ($hasAddedSignature && $hasRemovedSignature) {
            // Signature was modified - verify if it could be a breaking parameter change
            return [
                'change_type' => 'signature_changed',
                'is_breaking' => false, // Default non-breaking unless required parameter without default
                'description' => "Firma o parámetros del método {$methodName} modificados.",
            ];
        }

        return [
            'change_type' => 'modified',
            'is_breaking' => false,
            'description' => "Lógica interna del método {$methodName} actualizada.",
        ];
    }

    /**
     * Analyze composer.json or package.json changes.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function analyzeDependencies(string $path): array
    {
        $diff = $this->runGitCommand(['diff', 'HEAD', '--', $path]);
        $deps = [];

        if (empty($diff)) {
            return $deps;
        }

        $lines = explode("\n", $diff);
        foreach ($lines as $line) {
            if (preg_match('/^([+-])\s*"([^"]+)":\s*"([^"]+)"/', $line, $matches)) {
                $sign = $matches[1];
                $package = $matches[2];
                $version = $matches[3];

                if (in_array($package, ['name', 'description', 'version', 'scripts', 'keywords', 'author'], true)) {
                    continue;
                }

                $deps[] = [
                    'manager' => str_starts_with($path, 'composer') ? 'composer' : 'npm',
                    'package' => $package,
                    'version' => $version,
                    'change_type' => $sign === '+' ? 'added_or_updated' : 'removed_or_replaced',
                    'is_major' => false,
                ];
            }
        }

        return $deps;
    }

    /**
     * Get changed files from git status.
     *
     * @return array<int, array{path: string, status: string}>
     */
    protected function getGitStatusFiles(): array
    {
        $output = $this->runGitCommand(['status', '--porcelain']);
        if (empty($output)) {
            return [];
        }

        $files = [];
        $lines = explode("\n", trim($output));

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $statusCode = substr($line, 0, 2);
            $filePath = trim(substr($line, 3));

            // Handle renames e.g. old -> new
            if (str_contains($filePath, '->')) {
                $parts = explode('->', $filePath);
                $filePath = trim(end($parts));
            }

            $status = 'modified';
            if (str_contains($statusCode, '?') || str_contains($statusCode, 'A')) {
                $status = 'added';
            } elseif (str_contains($statusCode, 'D')) {
                $status = 'deleted';
            }

            $files[] = [
                'path' => $filePath,
                'status' => $status,
            ];
        }

        return $files;
    }

    /**
     * Get changed files between two git commits/refs.
     *
     * @return array<int, array{path: string, status: string}>
     */
    protected function getGitDiffFiles(?string $diffTarget = null): array
    {
        $args = ['diff', '--name-status'];
        if ($diffTarget) {
            $args[] = $diffTarget;
        } else {
            $args[] = 'HEAD~1';
        }

        $output = $this->runGitCommand($args);
        if (empty($output)) {
            return [];
        }

        $files = [];
        $lines = explode("\n", trim($output));

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $parts = preg_split('/\s+/', $line, 2);
            if (count($parts) < 2) {
                continue;
            }

            $code = strtoupper(trim($parts[0]));
            $filePath = trim($parts[1]);

            $status = 'modified';
            if (str_starts_with($code, 'A')) {
                $status = 'added';
            } elseif (str_starts_with($code, 'D')) {
                $status = 'deleted';
            }

            $files[] = [
                'path' => $filePath,
                'status' => $status,
            ];
        }

        return $files;
    }

    /**
     * Fallback to find recently modified files in app and resources.
     *
     * @return array<int, array{path: string, status: string}>
     */
    protected function getRecentlyModifiedFiles(): array
    {
        $files = [];
        $directories = [
            'app/Http/Controllers',
            'resources/views',
            'resources/js',
        ];

        $threshold = time() - (86400 * 2); // modified in last 48 hours

        foreach ($directories as $dir) {
            $fullDir = base_path($dir);
            if (! File::isDirectory($fullDir)) {
                continue;
            }

            $allFiles = File::allFiles($fullDir);
            foreach ($allFiles as $file) {
                if ($file->getMTime() >= $threshold) {
                    $files[] = [
                        'path' => str_replace(base_path().'/', '', $file->getPathname()),
                        'status' => 'modified',
                    ];
                }
            }
        }

        return $files;
    }

    /**
     * Get the latest commit hash from git.
     */
    public function getCurrentCommitHash(): ?string
    {
        $hash = $this->runGitCommand(['rev-parse', '--short', 'HEAD']);

        return $hash ? trim($hash) : null;
    }

    /**
     * Verify if a commit hash exists in git.
     */
    protected function isValidGitCommit(string $commitHash): bool
    {
        $output = $this->runGitCommand(['cat-file', '-t', $commitHash]);

        return trim((string) $output) === 'commit';
    }

    /**
     * Run a git command safely in the application base path.
     *
     * @param  array<int, string>  $args
     */
    protected function runGitCommand(array $args): string
    {
        try {
            $command = array_merge(['git'], $args);
            $process = new Process($command, base_path());
            $process->run();

            if ($process->isSuccessful()) {
                return trim($process->getOutput());
            }
        } catch (\Throwable $e) {
            // Git might not be accessible or fails in some environments
        }

        return '';
    }

    /**
     * Generate an intelligent automatic summary sentence.
     *
     * @param  array<string, int>  $stats
     */
    protected function generateAutoSummary(array $stats, string $type): string
    {
        $parts = [];

        if (! empty($stats['classes_count'])) {
            $parts[] = "{$stats['classes_count']} ".($stats['classes_count'] === 1 ? 'clase' : 'clases');
        }

        if (! empty($stats['methods_count'])) {
            $parts[] = "{$stats['methods_count']} ".($stats['methods_count'] === 1 ? 'método' : 'métodos');
        }

        if (! empty($stats['views_count'])) {
            $parts[] = "{$stats['views_count']} ".($stats['views_count'] === 1 ? 'vista' : 'vistas');
        }

        if (! empty($stats['scripts_count'])) {
            $parts[] = "{$stats['scripts_count']} ".($stats['scripts_count'] === 1 ? 'script/estilo' : 'scripts/estilos');
        }

        if (! empty($stats['updates_count'])) {
            $parts[] = "{$stats['updates_count']} ".($stats['updates_count'] === 1 ? 'actualización' : 'actualizaciones');
        }

        if (empty($parts)) {
            return "Mantenimiento general del sistema y actualización de versión {$type}.";
        }

        return 'Actualización con cambios en: '.implode(', ', $parts).'.';
    }
}
