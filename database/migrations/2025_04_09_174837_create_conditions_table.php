<?php

declare(strict_types=1);

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
        Schema::create('conditions', static function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('encounter_id')->constrained('encounters')->cascadeOnDelete();
            $table->boolean('primary_source');
            $table->foreignId('asserter_id')->nullable()->constrained('identifiers')->cascadeOnDelete();
            $table->foreignId('report_origin_id')->nullable()->constrained('codeable_concepts')->cascadeOnDelete();
            $table->foreignId('context_id')->constrained('identifiers')->cascadeOnDelete();
            $table->foreignId('code_id')->constrained('codeable_concepts')->cascadeOnDelete();
            $table->enum('clinical_status', ['active', 'finished', 'recurrence', 'remission', 'resolved']);
            $table->enum('verification_status', ['confirmed', 'differential', 'entered_in_error', 'provisional', 'refuted']);
            $table->foreignId('severity_id')->constrained('codeable_concepts')->cascadeOnDelete();
            $table->timestamp('onset_date');
            $table->timestamp('asserted_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conditions');
    }
};
