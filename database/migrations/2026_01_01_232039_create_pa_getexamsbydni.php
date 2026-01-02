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
        DB::unprepared("DROP PROCEDURE IF EXISTS PA_getExamsByDNI;");
        DB::unprepared(
            'CREATE PROCEDURE PA_getExamsByDNI(
                IN id_historia BIGINT(19)
            )
            BEGIN
                SELECT DATE(e.created_at), e.id
                FROM examenes e
                INNER JOIN historias h ON e.id_historia = h.id
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
        DB::unprepared("DROP PROCEDURE IF EXISTS getExamsByDNI;");
    }
};
