<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstadoCitaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            ['id' => 1, 'descripcion' => 'PENDIENTE'],
            ['id' => 2, 'descripcion' => 'CONFIRMADO'],
            ['id' => 3, 'descripcion' => 'CANCELADO'],
            ['id' => 4, 'descripcion' => 'REAGENDADO'],
            ['id' => 5, 'descripcion' => 'NO ASISTIO'],
            ['id' => 6, 'descripcion' => 'ATENDIDO'],
            ['id' => 7, 'descripcion' => 'EN ESPERA'],
        ];

        DB::table('estado_cita')->upsert($statuses, ['id'], ['descripcion']);
    }
}
