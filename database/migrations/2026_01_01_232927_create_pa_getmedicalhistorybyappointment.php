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
        DB::unprepared("DROP PROCEDURE IF EXISTS PA_getMedicalHistoryByAppointment;");
        DB::unprepared(
            'CREATE PROCEDURE PA_getMedicalHistoryByAppointment(
                IN id_appointment BIGINT(19)
            )
            BEGIN
                SELECT
                    h.id history,
                    c.id appointment,
                    UPPER(h.nombres) nombres,
                    h.dni, (YEAR(CURRENT_DATE) - YEAR(h.fecha_nacimiento)) - (RIGHT(CURRENT_DATE,5) < RIGHT(h.fecha_nacimiento, 5)) AS age
                FROM controles c
                INNER JOIN historias h ON c.id_historia = h.id
                WHERE c.deleted_at IS NULL AND h.deleted_at IS NULL AND c.id = id_appointment;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS getMedicalHistoryByAppointment;");
    }
};
