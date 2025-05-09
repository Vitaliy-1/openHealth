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
        Schema::create('encounters', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained('persons');
            $table->uuid()->unique();
            $table->enum('status', ['entered_in_error', 'finished']);
            $table->foreignId('visit_id')->constrained('identifiers')->cascadeOnDelete();
            $table->foreignId('episode_id')->constrained('identifiers')->cascadeOnDelete();
            $table->foreignId('class_id')->constrained('codings')->cascadeOnDelete();
            $table->foreignId('type_id')->constrained('codeable_concepts')->cascadeOnDelete();
            $table->foreignId('priority_id')->nullable()->constrained('codeable_concepts')->cascadeOnDelete();
            $table->foreignId('performer_id')->constrained('identifiers')->cascadeOnDelete();
            $table->foreignId('division_id')->constrained('identifiers')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('encounter_reasons', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('encounter_id')->constrained('encounters')->cascadeOnDelete();
            $table->foreignId('codeable_concept_id')->constrained('codeable_concepts')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('encounter_diagnoses', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('encounter_id')->constrained('encounters')->cascadeOnDelete();
            $table->foreignId('condition_id')->constrained('identifiers')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('codeable_concepts')->cascadeOnDelete();
            $table->integer('rank')->nullable();
            $table->timestamps();
        });

        Schema::create('encounter_actions', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('encounter_id')->constrained('encounters')->cascadeOnDelete();
            $table->foreignId('codeable_concept_id')->constrained('codeable_concepts')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encounter_actions');
        Schema::dropIfExists('encounter_diagnoses');
        Schema::dropIfExists('encounter_reasons');
        Schema::dropIfExists('encounters');
    }
};
