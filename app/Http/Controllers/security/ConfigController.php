<?php

namespace App\Http\Controllers\security;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreVersionValidate;
use App\Models\AppVersion;
use App\Services\SemanticVersioningService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    /**
     * ConfigController constructor.
     */
    public function __construct(public SemanticVersioningService $versioningService)
    {
        $this->middleware(['auth', 'prevent.back']);
        $this->middleware('permission:configuracion_acceder')->only('index');
        $this->middleware('permission:configuracion_ver')->only('list', 'show', 'detect');
        $this->middleware('permission:configuracion_crear')->only('generate');
        $this->middleware('permission:configuracion_borrar')->only('destroy');
    }

    /**
     * Render the Configuration & Versioning SPA index page.
     */
    public function index(): View
    {
        $user = auth()->user();
        $currentVersion = $this->versioningService->getCurrentVersion();
        $totalVersions = AppVersion::count();
        $latestRelease = AppVersion::orderByDesc('id')->first();

        $configState = [
            'permissions' => [
                'access' => $user?->can('configuracion_acceder') ?? false,
                'view' => $user?->can('configuracion_ver') ?? false,
                'create' => $user?->can('configuracion_crear') ?? false,
                'delete' => $user?->can('configuracion_borrar') ?? false,
            ],
            'current_version' => $currentVersion,
            'total_versions' => $totalVersions,
            'latest_release' => $latestRelease ? [
                'id' => $latestRelease->id,
                'version' => $latestRelease->version,
                'version_type' => $latestRelease->version_type,
                'summary' => $latestRelease->summary,
                'created_at' => $latestRelease->created_at?->format('Y-m-d H:i:s'),
            ] : null,
            'routes' => [
                'list' => route('security.config.list'),
                'detect' => route('security.config.detect'),
                'generate' => route('security.config.generate'),
            ],
        ];

        return view('security.config.index', compact('configState', 'currentVersion'));
    }

    /**
     * List all recorded semantic versions with pagination and filtering.
     */
    public function list(Request $request): JsonResponse
    {
        $search = trim((string) $request->input('search', ''));
        $type = strtoupper(trim((string) $request->input('type', '')));
        $query = AppVersion::with('creator:id,name,username,email')->orderByDesc('id');

        if ($type !== '' && in_array($type, ['MAJOR', 'MINOR', 'PATCH'], true)) {
            $query->where('version_type', $type);
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('version', 'LIKE', "%{$search}%")
                    ->orWhere('summary', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('commit_hash', 'LIKE', "%{$search}%");
            });
        }

        $user = auth()->user();
        $permissions = [
            'create' => $user?->can('configuracion_crear') ?? false,
            'delete' => $user?->can('configuracion_borrar') ?? false,
            'view' => $user?->can('configuracion_ver') ?? false,
        ];

        $pageSize = (int) $request->input('pageSize', 10);
        $paginator = $query->paginate($pageSize);

        $formatted = collect($paginator->items())->map(function (AppVersion $v) {
            $authorName = $v->creator ? ($v->creator->name ?: $v->creator->username) : 'Sistema';

            return [
                'id' => $v->id,
                'version' => $v->version,
                'version_type' => $v->version_type,
                'summary' => $v->summary,
                'description' => $v->description,
                'commit_hash' => $v->commit_hash,
                'classes_count' => count($v->classes_changed ?? []),
                'methods_count' => count($v->methods_changed ?? []),
                'views_count' => count($v->views_changed ?? []),
                'scripts_count' => count($v->scripts_changed ?? []),
                'dependencies_count' => count($v->dependencies_changed ?? []),
                'updates_count' => count($v->updates ?? []),
                'stats' => $v->stats ?? [],
                'author' => $authorName,
                'created_at' => $v->created_at?->format('Y-m-d H:i:s'),
                'created_at_human' => $v->created_at?->diffForHumans(),
            ];
        });

        // Summary counts for badges
        $metrics = [
            'total' => AppVersion::count(),
            'major' => AppVersion::where('version_type', 'MAJOR')->count(),
            'minor' => AppVersion::where('version_type', 'MINOR')->count(),
            'patch' => AppVersion::where('version_type', 'PATCH')->count(),
        ];

        return response()->json([
            'status' => true,
            'data' => $formatted,
            'total' => $paginator->total(),
            'currentPage' => $paginator->currentPage(),
            'lastPage' => $paginator->lastPage(),
            'pageSize' => $paginator->perPage(),
            'metrics' => $metrics,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Real-time scan and change detection of the codebase.
     */
    public function detect(): JsonResponse
    {
        $detection = $this->versioningService->detectPendingChanges();

        return response()->json([
            'status' => true,
            'detection' => $detection,
        ], 200);
    }

    /**
     * Generate and record a new semantic version snapshot.
     */
    public function generate(StoreVersionValidate $request): JsonResponse
    {
        $validated = $request->validated();
        $user = auth()->user();
        // If changes were not provided from modal, run detection automatically to populate payload
        if (empty($validated['classes_changed']) && empty($validated['views_changed']) && empty($validated['scripts_changed'])) {
            $detection = $this->versioningService->detectPendingChanges();
            $validated['classes_changed'] = $detection['classes_changed'];
            $validated['methods_changed'] = $detection['methods_changed'];
            $validated['views_changed'] = $detection['views_changed'];
            $validated['scripts_changed'] = $detection['scripts_changed'];
            $validated['dependencies_changed'] = $detection['dependencies_changed'];
            $validated['updates'] = $detection['updates'];
            $validated['stats'] = $detection['stats'];
            $validated['commit_hash'] = $validated['commit_hash'] ?? $detection['commit_hash'];
        }

        $appVersion = $this->versioningService->recordVersion($validated, $user?->id);

        return response()->json([
            'status' => true,
            'type' => 'success',
            'message' => "Versión v{$appVersion->version} ({$appVersion->version_type}) registrada exitosamente.",
            'version' => [
                'id' => $appVersion->id,
                'version' => $appVersion->version,
                'version_type' => $appVersion->version_type,
                'summary' => $appVersion->summary,
                'created_at' => $appVersion->created_at?->format('Y-m-d H:i:s'),
            ],
        ], 201);
    }

    /**
     * Show full details and change logs of a specific version.
     */
    public function show(int $id): JsonResponse
    {
        $version = AppVersion::with('creator:id,name,username,email')->findOrFail($id);

        $authorName = $version->creator ? ($version->creator->name ?: $version->creator->username) : 'Sistema';

        return response()->json([
            'status' => true,
            'version' => [
                'id' => $version->id,
                'version' => $version->version,
                'version_type' => $version->version_type,
                'summary' => $version->summary,
                'description' => $version->description,
                'commit_hash' => $version->commit_hash,
                'snapshot_hash' => $version->snapshot_hash,
                'classes_changed' => $version->classes_changed ?? [],
                'methods_changed' => $version->methods_changed ?? [],
                'views_changed' => $version->views_changed ?? [],
                'scripts_changed' => $version->scripts_changed ?? [],
                'dependencies_changed' => $version->dependencies_changed ?? [],
                'updates' => $version->updates ?? [],
                'stats' => $version->stats ?? [],
                'author' => $authorName,
                'author_email' => $version->creator?->email,
                'created_at' => $version->created_at?->format('Y-m-d H:i:s'),
                'created_at_human' => $version->created_at?->diffForHumans(),
            ],
        ], 200);
    }

    /**
     * Delete a recorded version.
     */
    public function destroy(int $id): JsonResponse
    {
        $version = AppVersion::findOrFail($id);
        if (AppVersion::count() <= 1) {
            return response()->json([
                'status' => false,
                'type' => 'warning',
                'message' => 'No es posible eliminar la única versión registrada en el sistema.',
            ], 422);
        }

        $deletedVersionNumber = $version->version;
        $version->delete();

        return response()->json([
            'status' => true,
            'type' => 'success',
            'message' => "La versión v{$deletedVersionNumber} fue eliminada del historial.",
        ], 200);
    }
}
