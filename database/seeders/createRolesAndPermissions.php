<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class createRolesAndPermissions extends Seeder {
    
    public function run(): void {
        // Roles
        //$administrador    = Role::create(['name' => 'administrador']);
        //$especialista     = Role::create(['name' => 'especialista']);
        //$asistente        = Role::create(['name' => 'asistente']);

        // Permisos
        $crudPermissions = ['acceder', 'ver', 'crear', 'actualizar', 'borrar'];
        $modulos = ['historias', 'examenes', 'controles', 'informes', 'riesgos', 'categorias', 'presentaciones', 'farmacos', 'diagnosticos', 'ocupaciones', 'empresa', 'especialidades', 'modulos', 'usuarios', 'roles', 'permisos', 'seguridad', 'documentos', 'mantenimiento', 'reportes', 'dashboard'];
        $modules = ['historia', 'examen', 'control', 'informe','riesgo', 'categoria', 'presentacion', 'farmaco', 'diagnostico', 'ocupacion', 'empresa', 'modulo', 'especialidad', 'usuario', 'rol', 'permiso'];

        foreach($modulos as $m) {
            Permission::create(['name' => $m]);
        }

        foreach ($modules as $module) {
            foreach ($crudPermissions as $action) {
                Permission::create(['name' => "{$module}_{$action}"]);
            }
        }

        $administrador = Role::find(1);
        $especialista = Role::find(2);
        $asistente = Role::find(3);

        // Asignar permisos a roles
        $administrador->givePermissionTo(Permission::all());
        $especialista->givePermissionTo(Permission::where('name', 'not like', '%seguridad%')->get());
        $asistente->givePermissionTo(Permission::where('name', '%_ver')->orWhere('name', '%_crear')->get());

        $user = User::find(1);
        $user->assignRole('administrador');

        $user = User::find(3);
        $user->assignRole('especialista');

        $user = User::find(4);
        $user->assignRole('especialista');

        $user = User::find(6);
        $user->assignRole('especialista');
    }
}
