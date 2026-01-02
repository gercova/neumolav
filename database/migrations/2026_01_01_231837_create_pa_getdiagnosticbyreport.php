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
        DB::unprepared("DROP PROCEDURE IF EXISTS PA_getDiagnosticByReport;");
        DB::unprepared(
            'CREATE PROCEDURE PA_getDiagnosticByReport(
                IN id_report BIGINT(19)
            )
            BEGIN
                SELECT d.descripcion diagnostic, id.id
                FROM informe_diagnostico id
                INNER JOIN diagnosticos d ON id.id_diagnostico = d.id
                INNER JOIN historias h ON id.id_historia = h.id
                WHERE id.deleted_at IS NULL AND d.deleted_at IS NULL AND h.deleted_at IS NULL AND id.id_informe = id_report;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP PROCEDURE IF EXISTS getDiagnosticByReport;");
    }
};
