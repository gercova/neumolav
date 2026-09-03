<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('module', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion', 100)->unique();
            $table->string('detalle', 200);
            $table->string('icono', 100)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('module');
    }
};