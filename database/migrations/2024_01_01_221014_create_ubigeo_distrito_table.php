<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ubigeo_distrito', function (Blueprint $table) {
            $table->char('id', 6)->primary();
            $table->string('nombre_distrito', 45)->nullable();
            $table->char('province_id', 4)->nullable();
            $table->char('department_id', 2)->nullable();
            $table->foreign('department_id')->references('id')->on('ubigeo_departamento');
            $table->foreign('province_id')->references('id')->on('ubigeo_provincia');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ubigeo_distrito');
    }
};