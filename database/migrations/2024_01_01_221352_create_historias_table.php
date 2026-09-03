<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historias', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('id_td');
            $table->string('dni', 11)->unique();
            $table->string('nombres', 150);
            $table->date('fecha_nacimiento');
            $table->char('id_sexo', 1);
            $table->char('telefono', 11);
            $table->string('email', 100)->nullable();
            $table->unsignedInteger('id_gs');
            $table->string('ubigeo_extranjero', 255)->nullable();
            $table->string('lugar_nacimiento', 100)->default('--');
            $table->string('lugar_residencia', 100)->default('--');
            $table->char('ubigeo_nacimiento', 6);
            $table->char('ubigeo_residencia', 6);
            $table->unsignedInteger('id_gi');
            $table->string('ocupacion', 50)->default('--');
            $table->unsignedInteger('id_ocupacion');
            $table->unsignedInteger('id_estado');
            $table->text('cirugias')->nullable();
            $table->text('transfusiones')->nullable();
            $table->text('traumatismos')->nullable();
            $table->text('hospitalizaciones')->nullable();
            $table->text('drogas')->nullable();
            $table->text('antecedentes')->nullable();
            $table->text('estadobasal')->nullable();
            $table->text('medicacion')->nullable();
            $table->text('animales')->nullable();
            $table->text('consumoagua')->nullable();
            $table->text('alimentacion')->nullable();
            $table->text('otros')->nullable();
            $table->text('asmabronquial')->nullable();
            $table->text('epoc')->nullable();
            $table->text('epid')->nullable();
            $table->text('tuberculosis')->nullable();
            $table->text('cancerpulmon')->nullable();
            $table->text('efusionpleural')->nullable();
            $table->text('neumonias')->nullable();
            $table->text('tabaquismo')->nullable();
            $table->unsignedInteger('id_ct')->default(4);
            $table->float('cig')->nullable();
            $table->float('aniosfum')->nullable();
            $table->float('result')->nullable();
            $table->text('contactotbc')->nullable();
            $table->text('exposicionbiomasa')->nullable();
            $table->text('motivoconsulta')->nullable();
            $table->text('sintomascardinales')->nullable();
            $table->text('te')->nullable();
            $table->text('fi')->nullable();
            $table->text('c')->nullable();
            $table->text('relatocronologico')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('id_td')->references('id')->on('tipo_documento');
            $table->foreign('id_sexo')->references('id')->on('sexo');
            $table->foreign('id_gs')->references('id')->on('grupo_sanguineo');
            $table->foreign('id_gi')->references('id')->on('grado_instruccion');
            $table->foreign('id_ocupacion')->references('id')->on('ocupaciones');
            $table->foreign('ubigeo_nacimiento')->references('id')->on('ubigeo_distrito');
            $table->foreign('ubigeo_residencia')->references('id')->on('ubigeo_distrito');
            $table->foreign('id_ct')->references('id')->on('tabaquismo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historias');
    }
};