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
        DB::unprepared("DROP PROCEDURE IF EXISTS PA_getReportsByMedicalHistory;");
        DB::unprepared(
            'CREATE PROCEDURE PA_getReportsByMedicalHistory(
                IN id_historia BIGINT(19)
            )
            BEGIN
                -- Seleccionar todos los reportes que pertenecen a una historia clínica especificada
                SELECT hi.created_at, hi.dni, hi.id
                FROM historia_informe hi
                JOIN historias h ON hi.id_historia = h.id
                WHERE hi.deleted_at IS NULL AND h.deleted_at IS NULL AND hi.id_historia = id_historia;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS getReportsByMedicalHistory;");
    }
};
