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
        DB::unprepared("DROP PROCEDURE IF EXISTS PA_getMedicationByAppointment;");
        DB::unprepared(
            'CREATE PROCEDURE PA_getMedicationByAppointment(
                IN id_appointment BIGINT(19)
            )
            BEGIN
                -- Seleccionar todos los productos que pertenecen a la categoría especificada
                SELECT d.descripcion as drug, cm.descripcion rp, cm.id
                FROM control_medicacion as cm
                INNER JOIN drogas d ON cm.id_droga = d.id
                INNER JOIN controles c ON cm.id_control = c.id
                INNER JOIN historias h ON cm.id_historia = h.id
                WHERE cm.deleted_at IS NULL AND c.deleted_at IS NULL AND h.deleted_at IS NULL AND cm.id_control = id_appointment;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS getMedicationByAppointment;");
    }
};
