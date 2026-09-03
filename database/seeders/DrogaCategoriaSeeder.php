<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DrogaCategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            ['descripcion' => 'A', 'detalle' => 'Fármacos sin riesgo para el embarazo'],
            ['descripcion' => 'B', 'detalle' => 'Fármaco con riesgo leve para el embarazo '],
            ['descripcion' => 'C', 'detalle' => 'Fármacos con riesgo de daño al feto'],
            ['descripcion' => 'D', 'detalle' => 'Fármacos con indicios de riesgo fetal'],
            ['descripcion' => 'X', 'detalle' => 'Contraindicados en el embarazo.'],
            ['descripcion' => 'T', 'detalle' => 'Solo para indicaciones que requieren tratamiento.'],
        ];

        foreach($categorias as $categoria){
            Category::create($categoria);
        }
    }
}
