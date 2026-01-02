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
        DB::unprepared("DROP PROCEDURE IF EXISTS PA_getRisksByMedicalHistory;");
        DB::unprepared(
            'CREATE PROCEDURE PA_getRisksByMedicalHistory(
                IN id_historia BIGINT(19)
            )
            BEGIN
                -- obtener Informes de Riesgo por DNI de Historia Clínica
                SELECT hr.created_at, hr.dni, hr.id
                FROM historia_riesgo hr
                INNER JOIN historias h ON hr.id_historia = h.id
                WHERE hr.deleted_at IS NULL AND h.deleted_at IS NULL AND hr.id_historia = id_historia
                ORDER BY hr.created_at DESC;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS getRisksByMedicalHistory;");
    }
};
