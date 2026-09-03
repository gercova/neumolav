<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TabaquismoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tabaquismo')->insert([
            ['consumo' => 'Cigarro'],
            ['consumo' => 'Tabaco'],
            ['consumo' => 'Pipa'],
            ['consumo' => 'NA'],
        ]);
    }
}
