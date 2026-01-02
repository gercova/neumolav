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
        DB::unprepared("DROP PROCEDURE IF EXISTS PA_getExamsbyMedicalHistory;");
        DB::unprepared(
            'CREATE PROCEDURE PA_getExamsbyMedicalHistory(
                IN id_historia BIGINT(19)
            )
            BEGIN
                -- Seleccionar todos los exámenes que pertenecen a una historia clínica especificada
                SELECT e.created_at, e.dni, te.descripcion examen, e.id
                FROM examenes e
                JOIN tipo_examen AS te ON e.id_tipo = te.id
                JOIN historias h ON e.id_historia = h.id
                WHERE e.deleted_at IS NULL AND h.deleted_at IS NULL AND e.id_historia = id_historia
                ORDER BY e.created_at DESC;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS getExamsbyMedicalHistory;");
    }
};
