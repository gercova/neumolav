<?php

namespace Database\Seeders;

use App\Models\Presentation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DrogaPresentacionSeeder extends Seeder
{
    public function run(): void
    {
        $presentaciones = [
            [
                'descripcion' => 'Tableta',
                'aka'         => 'Tab.',
            ],[
                'descripcion' => 'Capsula',
                'aka'         => 'Cap.',
            ],[
                'descripcion' => 'Ampolla',
                'aka'         => 'Amp.',
            ],[
                'descripcion' => 'Inhalador',
                'aka'         => 'Inh.',
            ],[
                'descripcion' => 'Frasco',
                'aka'         => 'Fsco.',
            ],[
                'descripcion' => 'Sobre',
                'aka'         => 'Sob.',
            ],[
                'descripcion' => 'Spray',
                'aka'         => 'Spry.',
            ],[
                'descripcion' => 'Tratamiento',
                'aka'         => 'Tto.',
            ],[
                'descripcion' => 'Crema',
                'aka'         => 'Crm.',
            ],[
                'descripcion' => 'Caja',
                'aka'         => 'Caj.',
            ],
        ];

        
        foreach($presentaciones as $item){
            Presentation::create($item);
        }
    }
}
