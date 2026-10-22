<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            [
                'name' => 'historias',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'examenes',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'controles',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'informes',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'riesgos',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'categorias',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'presentaciones',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'farmacos',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'diagnosticos',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'ocupaciones',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'empresa',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'especialidades',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'modulos',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'usuarios',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'roles',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'permisos',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'seguridad',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'documentos',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'mantenimiento',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'reportes',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'dashboard',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'historia_acceder',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'historia_ver',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'historia_crear',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'historia_actualizar',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'historia_borrar',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'examen_acceder',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'examen_ver',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'examen_crear',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'examen_actualizar',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'examen_borrar',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'control_acceder',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'control_ver',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'control_crear',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'control_actualizar',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'control_borrar',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'informe_acceder',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'informe_ver',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'informe_crear',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'informe_actualizar',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'informe_borrar',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'riesgo_acceder',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'riesgo_ver',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'riesgo_crear',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'riesgo_actualizar',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'riesgo_borrar',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'categoria_acceder',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'categoria_ver',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'categoria_crear',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'categoria_actualizar',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'categoria_borrar',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'presentacion_acceder',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'presentacion_ver',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'presentacion_crear',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'presentacion_actualizar',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'presentacion_borrar',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'farmaco_acceder',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'farmaco_ver',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'farmaco_crear',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'farmaco_actualizar',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'farmaco_borrar',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'diagnostico_acceder',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'diagnostico_ver',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'diagnostico_crear',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'diagnostico_actualizar',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'diagnostico_borrar',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'ocupacion_acceder',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'ocupacion_ver',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'ocupacion_crear',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'ocupacion_actualizar',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'ocupacion_borrar',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'empresa_acceder',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'empresa_ver',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'empresa_crear',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'empresa_actualizar',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'empresa_borrar',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'modulo_acceder',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'modulo_ver',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'modulo_crear',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'modulo_actualizar',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'modulo_borrar',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'especialidad_acceder',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'especialidad_ver',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'especialidad_crear',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'especialidad_actualizar',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'especialidad_borrar',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'usuario_acceder',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'usuario_ver',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'usuario_crear',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'usuario_actualizar',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'usuario_borrar',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'rol_acceder',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'rol_ver',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'rol_crear',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'rol_actualizar',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'rol_borrar',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'permiso_acceder',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'permiso_ver',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'permiso_crear',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'permiso_actualizar',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'permiso_borrar',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'posts',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'post_acceder',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'post_ver',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'post_crear',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'post_actualizar',
                'description' => null,
                'guard_name' => 'web',
            ],
            [
                'name' => 'post_borrar',
                'description' => null,
                'guard_name' => 'web',
            ],
        ];

        foreach ($items as $item) {
            DB::table('permissions')->insert($item);
        }
    }
}
