<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ubigeo_provincia', function (Blueprint $table) {
            $table->char('id', 4)->primary();
            $table->string('nombre_provincia', 45);
            $table->char('department_id', 2);
            $table->foreign('department_id')->references('id')->on('ubigeo_departamento');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ubigeo_provincia');
    }
};