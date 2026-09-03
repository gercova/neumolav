<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('especialidades', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_ocupacion');
            $table->string('descripcion', 50)->unique();
            $table->string('detalle', 100);
            $table->boolean('estado')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('id_ocupacion')->references('id')->on('ocupaciones');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('especialidades');
    }
};