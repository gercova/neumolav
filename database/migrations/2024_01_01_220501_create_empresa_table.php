<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresa', function (Blueprint $table) {
            $table->id();
            $table->string('razon_social', 150);
            $table->string('nombre_comercial', 150);
            $table->string('ruc', 11)->unique();
            $table->string('email', 100);
            $table->text('descripcion');
            $table->string('slogan', 255)->nullable();
            $table->string('frase', 255)->nullable();
            $table->text('mision');
            $table->text('vision');
            $table->text('ubigeo');
            $table->text('iframe_location')->nullable();
            $table->string('direccion', 100)->nullable();
            $table->string('pais', 50)->nullable();
            $table->string('codigo_pais', 3)->nullable();
            $table->string('telefono', 20);
            $table->string('pagina_web', 50)->nullable()->unique();
            $table->string('representante_legal', 100)->nullable();
            $table->string('foto_representante', 50)->nullable();
            $table->string('logo', 50)->nullable();
            $table->string('logo_mini', 50)->nullable();
            $table->string('logo_receta', 50)->nullable();
            $table->string('rubro', 50)->nullable();
            $table->date('fecha_creacion')->nullable();
            $table->string('autocomplete', 5)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresa');
    }
};