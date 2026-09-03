<?php

namespace Database\Seeders;

use App\Models\AppointmentStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EstadoCitaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AppointmentStatus::create(['descripcion' => 'PENDIENTE']);
        AppointmentStatus::create(['descripcion' => 'CONFIRMADO']);
        AppointmentStatus::create(['descripcion' => 'CANCELADO']);
        AppointmentStatus::create(['descripcion' => 'REAGENDADO']);
        AppointmentStatus::create(['descripcion' => 'NO ASISTIO']);
        AppointmentStatus::create(['descripcion' => 'ATENDIDO']);
        AppointmentStatus::create(['descripcion' => 'EN ESPERA']);
    }
}
