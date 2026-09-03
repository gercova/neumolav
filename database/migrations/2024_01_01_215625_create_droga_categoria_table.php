<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('droga_categoria', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion', 50)->unique();
            $table->string('detalle', 200)->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('droga_categoria');
    }
};