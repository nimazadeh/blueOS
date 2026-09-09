<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Central media registry (morph attachments).
 *
 * Originals live on the private `media` disk (config/filesystems.php) and are
 * delivered through the public MediaController route — never from webroot.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('model_type', 150)->nullable();
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('collection', 50)->default('default');
            $table->string('filename', 191);
            $table->string('path', 500);
            $table->string('disk', 20)->default('media');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size');
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('alt_text', 255)->nullable();
            $table->timestamps();

            $table->index(['model_type', 'model_id']);
            $table->index('collection');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
