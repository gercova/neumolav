<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('informe_diagnostico', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_informe');
            $table->unsignedBigInteger('id_historia')->nullable();
            $table->string('dni', 11);
            $table->unsignedBigInteger('id_diagnostico');
            $table->boolean('estado')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('id_informe')->references('id')->on('historia_informe');
            $table->foreign('id_historia')->references('id')->on('historias');
            $table->foreign('id_diagnostico')->references('id')->on('diagnosticos');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('informe_diagnostico');
    }
};