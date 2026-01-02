<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS PA_getDiagnosticByAppointment;");
        DB::unprepared(
            'CREATE PROCEDURE PA_getDiagnosticByAppointment(
                IN id_control BIGINT(19)
            )
            BEGIN
                SELECT d.descripcion diagnostic, cd.id
                FROM control_diagnostico cd
                INNER JOIN diagnosticos d ON cd.id_diagnostico = d.id
                INNER JOIN controles c ON cd.id_control = c.id
                INNER JOIN historias h ON cd.id_historia = h.id
                WHERE cd.deleted_at IS NULL AND c.deleted_at IS NULL AND h.deleted_at IS NULL AND cd.id_control = id_control;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS getDiagnosticByAppointment;");
    }
};
