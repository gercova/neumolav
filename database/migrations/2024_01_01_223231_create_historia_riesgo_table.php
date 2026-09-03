<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historia_riesgo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_historia')->nullable();
            $table->string('dni', 11);
            $table->text('motivo');
            $table->text('antecedente');
            $table->text('sintomas');
            $table->text('examen_fisico');
            $table->text('examen_complementario');
            $table->text('riesgo_neumologico');
            $table->text('sugerencia');
            $table->boolean('estado')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('id_historia')->references('id')->on('historias');
            $table->foreign('dni')->references('dni')->on('historias');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historia_riesgo');
    }
};