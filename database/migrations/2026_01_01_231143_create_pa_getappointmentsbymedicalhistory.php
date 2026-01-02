<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS PA_getAppointmentsByMedicalHistory;");
        DB::unprepared(
            'CREATE PROCEDURE PA_getAppointmentsByMedicalHistory(
                IN id_historia BIGINT(19)
            )
            BEGIN
                -- obtener controles por DNI de Historia Clínica
                SELECT c.created_at, c.dni, c.id
                FROM controles c
                INNER JOIN historias h ON c.id_historia = h.id
                WHERE c.deleted_at IS NULL AND h.deleted_at IS NULL AND c.id_historia = id_historia
                ORDER BY c.created_at DESC;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS getAppointmentsByMedicalHistory;");
    }
};
