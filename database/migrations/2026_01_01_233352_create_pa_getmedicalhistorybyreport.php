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
        DB::unprepared("DROP PROCEDURE IF EXISTS PA_getMedicalHistoryByReport;");
        DB::unprepared(
            'CREATE PROCEDURE PA_getMedicalHistoryByReport(
                IN id_informe BIGINT(19)
            )
            BEGIN
                SELECT
                    h.id history,
                    hi.id informe,
                    UPPER(h.nombres) nombres,
                    h.dni, (YEAR(CURRENT_DATE) - YEAR(h.fecha_nacimiento)) - (RIGHT(CURRENT_DATE,5) < RIGHT(h.fecha_nacimiento, 5)) AS age
                FROM historia_informe hi
                INNER JOIN historias h ON hi.id_historia = h.id
                WHERE hi.deleted_at IS NULL AND h.deleted_at IS NULL AND hi.id = id_informe;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS getMedicalHistoryByReport;");
    }
};
