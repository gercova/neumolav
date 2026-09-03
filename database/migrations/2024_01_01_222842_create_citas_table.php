<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_historia');
            $table->string('descripcion', 150)->nullable();
            $table->unsignedInteger('id_estado')->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('id_historia')->references('id')->on('historias');
            $table->foreign('id_estado')->references('id')->on('estado_cita');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};