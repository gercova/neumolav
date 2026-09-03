<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $module = [
            [
                'descripcion' => 'Inicio',
                'detalle' => 'Módulo de Inicio',
                'icono' => '<i class=\"bi bi-house\"></i>',
            ],
            [
                'descripcion' => 'Documentos',
                'detalle' => 'Módulo de Historias Clínicas',
                'icono' => '<i class=\"bi bi-heart-pulse\"></i>',
            ],
            [
                'descripcion' => 'Mantenimiento',
                'detalle' => 'Módulo de Mantenimiento',
                'icono' => '<i class=\"bi bi-house-gear\"></i>',
            ],
            [
                'descripcion' => 'Seguridad',
                'detalle' => 'Módulo de Seguridad',
                'icono' => '<i class=\"bi bi-shield-lock\"></i>',
            ],
            [
                'descripcion' => 'Informes de Riesgo',
                'detalle' => 'Módulo de Informes de Riesgo',
                'icono' => '<i class=\"bi bi-heart-pulse\"></i>',
            ],
            [
                'descripcion' => 'Diagnósticos',
                'detalle' => 'Módulo de Diagnósticos',
                'icono' => '<i class=\"bi bi-virus2\"></i>',
            ],

        ];

        $submodule = [
            
        ];

        foreach ($module as $item) {
            Module::create($item);
        }
    }
}
