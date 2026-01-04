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
        DB::unprepared("DROP PROCEDURE IF EXISTS PA_getDiagnosticByExam;");
        DB::unprepared(
            'CREATE PROCEDURE PA_getDiagnosticByExam(
                IN id_exam BIGINT(19)
            )
            BEGIN
                SELECT d.descripcion diagnostic, ed.id
                FROM examen_diagnostico ed
                INNER JOIN diagnosticos d ON ed.id_diagnostico = d.id
                INNER JOIN examenes e ON ed.id_examen = e.id
                INNER JOIN historias h ON ed.id_historia = h.id
                WHERE ed.deleted_at IS NULL AND e.deleted_at IS NULL AND h.deleted_at IS NULL AND ed.id_examen = id_exam;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS getDiagnosticByExam;");
    }
};
