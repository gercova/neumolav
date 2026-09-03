<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drogas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_categoria');
            $table->unsignedBigInteger('id_presentacion');
            $table->string('descripcion', 50);
            $table->string('detalle', 100);
            $table->boolean('estado')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('id_categoria')->references('id')->on('droga_categoria');
            $table->foreign('id_presentacion')->references('id')->on('droga_presentacion');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drogas');
    }
};