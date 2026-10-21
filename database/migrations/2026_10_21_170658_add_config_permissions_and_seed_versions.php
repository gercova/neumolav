<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $permissions = [
            'configuracion',
            'configuracion_acceder',
            'configuracion_ver',
            'configuracion_crear',
            'configuracion_actualizar',
            'configuracion_borrar',
        ];

        foreach ($permissions as $name) {
            \Spatie\Permission\Models\Permission::findOrCreate($name, 'web');
        }

        $adminRole = \Spatie\Permission\Models\Role::where('name', 'administrador')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        // Registrar versión base inicial si no existe ninguna
        if (\Illuminate\Support\Facades\Schema::hasTable('app_versions')) {
            $exists = \Illuminate\Support\Facades\DB::table('app_versions')->where('version', '3.3.0')->exists();
            if (! $exists) {
                \Illuminate\Support\Facades\DB::table('app_versions')->insert([
                    'version' => '3.3.0',
                    'version_type' => 'MINOR',
                    'summary' => 'Actualización de arquitectura SPA, navegación híbrida y rediseño de reportes',
                    'description' => 'Versión consolidada con soporte completo de navegación SPA híbrida con Vue 3, Pinia, rediseño de informes médicos y recetas PDF.',
                    'commit_hash' => 'c57e736',
                    'classes_changed' => json_encode(['App\Http\Controllers\hcl\ExamsController', 'App\Http\Controllers\hcl\ReportsController', 'App\Http\Controllers\hcl\RisksController']),
                    'methods_changed' => json_encode([
                        [
                            'class' => 'App\Http\Controllers\hcl\ExamsController',
                            'method' => 'printPrescription',
                            'change_type' => 'modified',
                            'parameters' => [
                                ['name' => 'ex', 'type' => 'int', 'is_optional' => false, 'default' => null],
                                ['name' => 'format', 'type' => 'string', 'is_optional' => false, 'default' => 'a4'],
                            ],
                        ],
                    ]),
                    'views_changed' => json_encode(['resources/views/hcl/exams/pdf-a4.blade.php', 'resources/views/hcl/exams/pdf-a5.blade.php', 'resources/views/hcl/reports/pdf.blade.php']),
                    'scripts_changed' => json_encode(['resources/js/spa-engine.js', 'resources/css/app.css']),
                    'dependencies_changed' => json_encode([]),
                    'updates' => json_encode(['database/migrations/2026_10_18_160000_sync_estado_cita_table.php']),
                    'stats' => json_encode(['total_files' => 6, 'classes' => 3, 'methods' => 1, 'views' => 3, 'scripts' => 2]),
                    'snapshot_hash' => 'c57e736b9d36e6c9fb1ca78ddba8b3f93a551a9c',
                    'created_by' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $permissions = [
            'configuracion',
            'configuracion_acceder',
            'configuracion_ver',
            'configuracion_crear',
            'configuracion_actualizar',
            'configuracion_borrar',
        ];

        \Spatie\Permission\Models\Permission::whereIn('name', $permissions)->delete();
        \Illuminate\Support\Facades\DB::table('app_versions')->where('version', '3.3.0')->delete();
    }
};
