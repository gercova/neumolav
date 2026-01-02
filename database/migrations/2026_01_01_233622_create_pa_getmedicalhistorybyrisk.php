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
        DB::unprepared("DROP PROCEDURE IF EXISTS PA_getMedicalHistoryByRisk;");
        DB::unprepared(
            'CREATE PROCEDURE PA_getMedicalHistoryByRisk(
                IN id_informe BIGINT(19)
            )
            BEGIN
		        -- obtener historia clínica por DNI de ID Informe riesgo
                SELECT
                    hr.id report,
                    h.id history,
                    UPPER(h.nombres) nombres,
                    h.dni,
                    (YEAR(CURRENT_DATE) - YEAR(h.fecha_nacimiento)) - (RIGHT(CURRENT_DATE,5) < RIGHT(h.fecha_nacimiento, 5)) AS age
                FROM historia_riesgo hr
                INNER JOIN historias h ON hr.id_historia = h.id
                WHERE hr.deleted_at IS NULL AND h.deleted_at IS NULL AND hr.id = id_informe;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS getMedicalHistoryByRisk;");
    }
};
