<?php

namespace Database\Seeders;

use App\Models\Specialty;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EspecialidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $especialidades = [
            [
                'id_ocupacion'  => 22,
                'descripcion'   => 'Administrador (a)',
                'detalle'       => 'Ing. de Sistemas',
            ],
            [
                'id_ocupacion'  => 1,
                'descripcion'   => 'Neumólogo',
                'detalle'       => '--',
            ],
            [
                'id_ocupacion'  => 8,
                'descripcion'   => 'Enfermero (a)',
                'detalle'       => '--',
            ],
        ];

        foreach($especialidades as $item){
            Specialty::create($item);
        }
    }
}
