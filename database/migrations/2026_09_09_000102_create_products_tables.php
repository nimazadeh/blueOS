<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Products domain: products, product_features, technologies + pivot.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('slug', 191)->unique();
            $table->string('excerpt', 500)->nullable();
            $table->text('description')->nullable();
            $table->string('status', 20)->default('draft'); // draft|review|published|archived
            $table->string('type', 30)->default('software'); // software|saas|template|other
            $table->boolean('featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('featured');
            $table->index('published_at');
        });

        Schema::create('product_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('title', 200);
            $table->text('description')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['product_id', 'sort_order']);
        });

        Schema::create('technologies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 60);
            $table->string('slug', 191)->unique();
            $table->string('icon', 100)->nullable();
            $table->string('category', 40)->nullable();
            $table->timestamps();
        });

        Schema::create('product_technology', function (Blueprint $table) {
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('technology_id')->constrained()->cascadeOnDelete();
            $table->primary(['product_id', 'technology_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_technology');
        Schema::dropIfExists('product_features');
        Schema::dropIfExists('technologies');
        Schema::dropIfExists('products');
    }
};
