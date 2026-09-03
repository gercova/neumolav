<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('type_id')->default(1);
            $table->string('titulo', 150)->unique();
            $table->string('titulo_corto', 255)->nullable()->unique();
            $table->string('url', 150);
            $table->string('img', 100);
            $table->mediumText('descrip_img');
            $table->mediumText('alt_img');
            $table->mediumText('resumen');
            $table->mediumText('contenido');
            $table->string('categories', 255);
            $table->string('meta_content', 255);
            $table->string('key_words', 255);
            $table->string('etiquetas', 200)->default('--');
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('autor_post');
            $table->foreign('type_id')->references('id')->on('post_type');
            $table->foreign('autor_post')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post');
    }
};