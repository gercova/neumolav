<?php

namespace Database\Seeders;

use App\Models\MaritalStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EstadoCivilSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estadosciviles = [
            ['descripcion' => 'Soltero(a)'],
            ['descripcion' => 'Casado(a)'],
            ['descripcion' => 'Divorciado(a)'],
            ['descripcion' => 'Viudo(a)'],
            ['descripcion' => 'Conviviente'],
        ];

        foreach($estadosciviles as $item){
            MaritalStatus::create($item);
        }
    }
}
