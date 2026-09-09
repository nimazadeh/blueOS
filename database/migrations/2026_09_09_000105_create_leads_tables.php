<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Leads domain: inbox, notes and status history.
 *
 * Leads are never hard-deleted while an entity remains in the pipeline;
 * archived states use the status pipeline, not row deletion.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('email', 190);
            $table->string('phone', 40)->nullable();
            $table->string('company', 120)->nullable();
            $table->string('project_type', 60)->nullable();
            $table->text('message');
            // new|reviewing|contacted|proposal_sent|won|lost
            $table->string('status', 20)->default('new');
            $table->string('source', 40)->default('website');
            $table->timestamps();

            $table->index('status');
            $table->index(['status', 'created_at']);
        });

        Schema::create('lead_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->text('note');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();

            $table->index('lead_id');
        });

        Schema::create('lead_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained()->cascadeOnDelete();
            $table->string('old_status', 20)->nullable();
            $table->string('new_status', 20);
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            // History is append-only: no updated_at.
            $table->timestamp('created_at')->nullable();

            $table->index(['lead_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_status_history');
        Schema::dropIfExists('lead_notes');
        Schema::dropIfExists('leads');
    }
};
