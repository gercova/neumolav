<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('imagenes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_examen');
            $table->unsignedBigInteger('id_historia')->nullable();
            $table->string('dni', 11);
            $table->date('fecha_examen');
            $table->string('imagen', 50);
            $table->boolean('estado')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('id_examen')->references('id')->on('examenes');
            $table->foreign('id_historia')->references('id')->on('historias');
            $table->foreign('dni')->references('dni')->on('historias');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imagenes');
    }
};