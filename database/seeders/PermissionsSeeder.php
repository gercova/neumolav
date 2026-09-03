<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
                'name'          => 'historias',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'examenes',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'controles',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'informes',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'riesgos',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'categorias',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'presentaciones',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'farmacos',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'diagnosticos',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'ocupaciones',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'empresa',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'especialidades',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'modulos',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'usuarios',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'roles',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'permisos',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'seguridad',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'documentos',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'mantenimiento',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'reportes',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'dashboard',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'historia_acceder',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'historia_ver',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'historia_crear',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'historia_actualizar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'historia_borrar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'examen_acceder',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'examen_ver',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'examen_crear',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'examen_actualizar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'examen_borrar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'control_acceder',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'control_ver',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'control_crear',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'control_actualizar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'control_borrar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'informe_acceder',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'informe_ver',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'informe_crear',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'informe_actualizar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'informe_borrar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'riesgo_acceder',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'riesgo_ver',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'riesgo_crear',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'riesgo_actualizar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'riesgo_borrar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'categoria_acceder',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'categoria_ver',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'categoria_crear',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'categoria_actualizar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'categoria_borrar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'presentacion_acceder',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'presentacion_ver',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'presentacion_crear',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'presentacion_actualizar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'presentacion_borrar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'farmaco_acceder',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'farmaco_ver',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'farmaco_crear',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'farmaco_actualizar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'farmaco_borrar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'diagnostico_acceder',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'diagnostico_ver',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'diagnostico_crear',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'diagnostico_actualizar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'diagnostico_borrar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'ocupacion_acceder',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'ocupacion_ver',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'ocupacion_crear',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'ocupacion_actualizar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'ocupacion_borrar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'empresa_acceder',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'empresa_ver',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'empresa_crear',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'empresa_actualizar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'empresa_borrar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'modulo_acceder',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'modulo_ver',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'modulo_crear',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'modulo_actualizar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'modulo_borrar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'especialidad_acceder',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'especialidad_ver',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'especialidad_crear',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'especialidad_actualizar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'especialidad_borrar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'usuario_acceder',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'usuario_ver',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'usuario_crear',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'usuario_actualizar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'usuario_borrar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'rol_acceder',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'rol_ver',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'rol_crear',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'rol_actualizar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'rol_borrar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'permiso_acceder',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'permiso_ver',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'permiso_crear',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'permiso_actualizar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'permiso_borrar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'posts',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'post_acceder',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'post_ver',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'post_crear',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'post_actualizar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
            [
                'name'          => 'post_borrar',
                'description'   => NULL,
                'guard_name'    => 'web',
            ],
        ];

        foreach($items as $item){
            DB::table('permissions')->insert($item);
        }
    }
}
