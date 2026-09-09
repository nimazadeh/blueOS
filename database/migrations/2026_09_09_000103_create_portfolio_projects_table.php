<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Portfolio domain: case studies + technology pivot.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('portfolio_projects', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('slug', 191)->unique();
            $table->string('summary', 500)->nullable();
            $table->text('challenge')->nullable();
            $table->text('solution')->nullable();
            $table->text('results')->nullable();
            $table->string('status', 20)->default('draft'); // draft|review|published|archived
            $table->boolean('featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('featured');
            $table->index('published_at');
        });

        Schema::create('portfolio_project_technology', function (Blueprint $table) {
            $table->foreignId('portfolio_project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('technology_id')->constrained()->cascadeOnDelete();
            $table->primary(['portfolio_project_id', 'technology_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_project_technology');
        Schema::dropIfExists('portfolio_projects');
    }
};
