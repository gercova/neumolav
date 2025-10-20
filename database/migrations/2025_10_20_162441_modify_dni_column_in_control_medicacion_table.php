<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('control_medicacion', function (Blueprint $table) {
            // 1. Eliminar la clave foránea
            $table->dropForeign('fk_dni_control_medicacion_historias');
            
            // 2. Modificar la columna dni de varchar(8) a varchar(11)
            $table->string('dni', 11)->change();
            
            // 3. Volver a crear la clave foránea
            $table->foreign('dni', 'fk_dni_control_medicacion_historias')
                ->references('dni')
                ->on('historias') // Ajusta el nombre de la tabla relacionada
                ->onDelete('cascade') // o 'restrict', 'set null' según necesites
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('control_medicacion', function (Blueprint $table) {
            // Revertir los cambios
            $table->dropForeign('fk_dni_control_medicacion_historias');
            
            $table->string('dni', 8)->change();
            
            $table->foreign('dni', 'fk_dni_control_medicacion_historias')
                ->references('dni')
                ->on('historias')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }
};
