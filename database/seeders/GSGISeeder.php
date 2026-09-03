<?php

namespace Database\Seeders;

use App\Models\BloodGroups;
use App\Models\DegreesInstruction;
use App\Models\Sex;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GSGISeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        Sex::create(['id' => 'F', 'descripcion' => 'Femenino']);
        Sex::create(['id' => 'M', 'descripcion' => 'Masculino']);
        
        $grado_instruccion = [
            'Primaria completa','Primaria incompleta','Secundaria completa','Secundaria incompleta','Universitaria completa','Universitaria incompleta','Técnica completa','Técnica incompleta','Sin grado'
        ];
        foreach ($grado_instruccion as $grado) {
            DegreesInstruction::create(['descripcion' => $grado]);
        }

        $grupo_sanguineo = ['A+','A-','B+','B-','AB+','AB-','O+','O-','Sin datos'];
        foreach ($grupo_sanguineo as $grupo) {
            BloodGroups::create(['descripcion' => $grupo]);
        }
    }
}
