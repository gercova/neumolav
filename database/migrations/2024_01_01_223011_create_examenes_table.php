<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('examenes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_historia')->nullable();
            $table->string('dni', 11);
            $table->unsignedInteger('id_tipo');
            $table->string('ta', 15);
            $table->string('fc', 15);
            $table->string('rf', 15);
            $table->string('so2', 15);
            $table->string('peso', 15);
            $table->string('talla', 15);
            $table->float('imc')->default(0);
            $table->text('pym');
            $table->text('typ');
            $table->text('cv');
            $table->text('abdomen');
            $table->text('hemolinfopoyetico');
            $table->text('tcs');
            $table->text('neurologico');
            $table->text('hemograma');
            $table->text('bioquimico');
            $table->text('perfilhepatico');
            $table->text('perfilcoagulacion');
            $table->text('perfilreumatologico');
            $table->text('orina');
            $table->text('sangre');
            $table->text('esputo');
            $table->text('heces');
            $table->text('lcr');
            $table->text('citoquimico');
            $table->text('adalp');
            $table->text('paplp');
            $table->text('bclp');
            $table->text('cgchlp');
            $table->text('cbklp');
            $table->text('bkdab');
            $table->text('bkcab');
            $table->text('cgchab');
            $table->text('papab');
            $table->text('bcab');
            $table->text('pulmon');
            $table->text('pleurabpp');
            $table->text('funcionpulmonar');
            $table->text('medicinanuclear')->nullable();
            $table->text('plandiag')->nullable();
            $table->text('plan')->nullable();
            $table->text('otros')->nullable();
            $table->boolean('estado')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('id_historia')->references('id')->on('historias');
            $table->foreign('dni')->references('dni')->on('historias');
            $table->foreign('id_tipo')->references('id')->on('tipo_examen');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('examenes');
    }
};