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
        Schema::create('immunizations', static function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('encounter_id')->constrained('encounters')->cascadeOnDelete();
            $table->enum('status', ['completed', 'entered_in_error']);
            $table->boolean('not_given');
            $table->foreignId('vaccine_code_id')->constrained('codeable_concepts')->cascadeOnDelete();
            $table->foreignId('context_id')->constrained('identifiers')->cascadeOnDelete();
            $table->timestamp('date');
            $table->boolean('primary_source');
            $table->foreignId('performer_id')->nullable()->constrained('identifiers')->cascadeOnDelete();
            $table->foreignId('report_origin_id')->nullable()->constrained('codeable_concepts')->cascadeOnDelete();
            $table->string('manufacturer')->nullable();
            $table->string('lot_number')->nullable();
            $table->date('expiration_date')->nullable();
            $table->foreignId('site_id')->nullable()->constrained('codeable_concepts')->cascadeOnDelete();
            $table->foreignId('route_id')->nullable()->constrained('codeable_concepts')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('immunization_dose_quantities', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('immunization_id')->constrained('immunizations')->cascadeOnDelete();
            $table->integer('value');
            $table->string('comparator')->nullable();
            $table->enum('unit', ['мг', 'мкг', 'мл'])->nullable();
            $table->string('system')->nullable();
            $table->string('code')->nullable();
            $table->timestamps();
        });

        Schema::create('immunization_explanations', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('immunization_id')->constrained('immunizations')->cascadeOnDelete();
            $table->foreignId('reasons_id')->nullable()->constrained('codeable_concepts')->cascadeOnDelete();
            $table->foreignId('reasons_not_given_id')->nullable()->constrained('codeable_concepts')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('immunization_vaccination_protocols', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('immunization_id')->constrained('immunizations')->cascadeOnDelete();
            $table->integer('dose_sequence')->nullable();
            $table->string('description')->nullable();
            $table->foreignId('authority_id')->nullable()->constrained('codeable_concepts')->cascadeOnDelete();
            $table->string('series')->nullable();
            $table->integer('series_doses')->nullable();
            $table->timestamps();
        });

        Schema::create('immunization_vaccination_protocol_target_diseases', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('vaccination_protocol_id')->constrained('immunization_vaccination_protocols')->cascadeOnDelete();
            $table->foreignId('codeable_concept_id')->nullable()->constrained('codeable_concepts')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('immunization_vaccination_protocol_target_diseases');
        Schema::dropIfExists('immunization_vaccination_protocols');
        Schema::dropIfExists('immunization_explanations');
        Schema::dropIfExists('immunization_dose_quantities');
        Schema::dropIfExists('immunizations');
    }
};
