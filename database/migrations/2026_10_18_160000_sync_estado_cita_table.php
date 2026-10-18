<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasTable('estado_cita')) {
            return;
        }

        // 1. Verificar si el id 2 actual es 'ATENDIDO'
        $statusTwo = DB::table('estado_cita')->where('id', 2)->first();
        $wasTwoAtendido = $statusTwo && strtoupper(trim($statusTwo->descripcion)) === 'ATENDIDO';

        // 2. Insertar o sincronizar los 7 estados estándar del sistema
        $statuses = [
            ['id' => 1, 'descripcion' => 'PENDIENTE'],
            ['id' => 2, 'descripcion' => 'CONFIRMADO'],
            ['id' => 3, 'descripcion' => 'CANCELADO'],
            ['id' => 4, 'descripcion' => 'REAGENDADO'],
            ['id' => 5, 'descripcion' => 'NO ASISTIO'],
            ['id' => 6, 'descripcion' => 'ATENDIDO'],
            ['id' => 7, 'descripcion' => 'EN ESPERA'],
        ];

        // Asegurar que exista el id 6 primero para no violar FK si se actualizan citas
        if (! DB::table('estado_cita')->where('id', 6)->exists()) {
            DB::table('estado_cita')->insert(['id' => 6, 'descripcion' => 'ATENDIDO']);
        }

        // Si el id 2 anterior era 'ATENDIDO', migrar las citas con id_estado = 2 a id_estado = 6
        if ($wasTwoAtendido && Schema::hasTable('citas')) {
            DB::table('citas')->where('id_estado', 2)->update(['id_estado' => 6]);
        }

        // Upsert de todos los estados oficiales
        DB::table('estado_cita')->upsert($statuses, ['id'], ['descripcion']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No revertimos catálogo para evitar dejar citas huérfanas
    }
};
