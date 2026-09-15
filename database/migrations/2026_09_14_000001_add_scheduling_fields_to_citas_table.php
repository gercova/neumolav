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
        Schema::table('citas', function (Blueprint $table) {
            $table->date('fecha_cita')->nullable()->after('id_historia')->index();
            $table->time('hora_cita')->nullable()->after('fecha_cita');
            $table->unsignedInteger('numero_turno')->nullable()->after('hora_cita');
            $table->string('motivo', 255)->nullable()->after('numero_turno');
            $table->text('observaciones')->nullable()->after('motivo');
            $table->index(['fecha_cita', 'id_estado']);
            $table->index(['id_historia', 'fecha_cita']);
        });

        // Backfill existing rows with fecha_cita from created_at
        DB::statement("UPDATE citas SET fecha_cita = DATE(created_at) WHERE fecha_cita IS NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropIndex(['citas_fecha_cita_index']);
            $table->dropIndex(['citas_fecha_cita_id_estado_index']);
            $table->dropIndex(['citas_id_historia_fecha_cita_index']);
            $table->dropColumn(['fecha_cita', 'hora_cita', 'numero_turno', 'motivo', 'observaciones']);
        });
    }
};
