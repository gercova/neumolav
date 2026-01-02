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
        DB::unprepared("DROP PROCEDURE IF EXISTS PA_getAppointmentsByHC;");
        DB::unprepared(
            'CREATE PROCEDURE PA_getAppointmentsByHC(
                IN id_historia BIGINT(19)
            )
            BEGIN
                SELECT DATE(c.created_at) created_at, c.id
                FROM controles c
                JOIN historias h ON c.id_historia = h.id
                WHERE c.deleted_at IS NULL AND h.deleted_at IS NULL AND c.id_historia = id_historia
                ORDER BY c.created_at DESC;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS getAppointmentsByDNI;");
    }
};
