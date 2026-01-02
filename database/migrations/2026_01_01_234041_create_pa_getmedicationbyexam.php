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
        DB::unprepared("DROP PROCEDURE IF EXISTS PA_getMedicationByExam;");
        DB::unprepared(
            'CREATE PROCEDURE PA_getMedicationByExam(
                IN id_exam BIGINT(19)
            )

            BEGIN
                SELECT d.descripcion as drug, em.descripcion rp, em.id
                FROM examen_medicacion em
                INNER JOIN drogas d ON em.id_droga = d.id
                INNER JOIN examenes e ON em.id_examen = e.id
                INNER JOIN historias h ON em.id_historia = h.id
                WHERE em.deleted_at IS NULL AND e.deleted_at IS NULL AND h.deleted_at IS NULL AND e.id = id_exam;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS getMedicationByExam;");
    }
};
