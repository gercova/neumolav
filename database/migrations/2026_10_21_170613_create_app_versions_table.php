<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('app_versions', function (Blueprint $table) {
            $table->id();
            $table->string('version', 50)->unique();
            $table->string('version_type', 20)->default('PATCH'); // MAJOR, MINOR, PATCH
            $table->string('summary', 255);
            $table->longText('description')->nullable();
            $table->string('commit_hash', 40)->nullable();
            $table->json('classes_changed')->nullable();
            $table->json('methods_changed')->nullable();
            $table->json('views_changed')->nullable();
            $table->json('scripts_changed')->nullable();
            $table->json('dependencies_changed')->nullable();
            $table->json('updates')->nullable();
            $table->json('stats')->nullable();
            $table->string('snapshot_hash', 64)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_versions');
    }
};
