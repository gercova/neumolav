<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('control_medicacion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_control')->nullable();
            $table->unsignedBigInteger('id_historia')->nullable();
            $table->string('dni', 11);
            $table->unsignedBigInteger('id_droga')->nullable();
            $table->string('descripcion', 200)->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('id_control')->references('id')->on('controles');
            $table->foreign('id_historia')->references('id')->on('historias');
            $table->foreign('dni')->references('dni')->on('historias')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('id_droga')->references('id')->on('drogas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('control_medicacion');
    }
};