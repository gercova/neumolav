<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('controles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_historia')->nullable();
            $table->string('dni', 11);
            $table->text('sintomas')->default('NO');
            $table->text('diagnostico')->default('NO');
            $table->text('plan')->default('NO');
            $table->text('tratamiento')->default('NO');
            $table->string('recomendaciones', 255)->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('id_historia')->references('id')->on('historias');
            $table->foreign('dni')->references('dni')->on('historias');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('controles');
    }
};