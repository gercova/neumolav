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
        DB::unprepared("DROP PROCEDURE IF EXISTS PA_getImgByExam;");
        DB::unprepared(
            'CREATE PROCEDURE PA_getImgByExam(
                IN id_exam BIGINT(19)
            )
            BEGIN
                SELECT i.imagen, i.fecha_examen, i.created_at, i.id
                FROM imagenes i
                JOIN examenes e ON i.id_examen = e.id
                WHERE i.deleted_at IS NULL AND e.deleted_at IS NULL AND e.id = id_exam;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS getImgByExam;");
    }
};
