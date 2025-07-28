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
        Schema::create('clinical_impressions', static function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('encounter_internal_id')->constrained('encounters');
            $table->enum('status', ['completed', 'entered_in_error']);
            $table->text('description')->nullable()->comment('Some description of the clinical impression');
            $table->foreignId('code_id')->constrained('codeable_concepts');
            $table->foreignId('encounter_id')->constrained('identifiers');
            $table->foreignId('assessor_id')->comment('author of the clinical impression')->constrained('identifiers');
            $table->foreignId('previous_id')->nullable()->constrained('identifiers');
            $table->text('summary')->nullable()->comment('Some summary');
            $table->text('note')->nullable()->comment('Some note');
            $table->timestamps();
        });

        Schema::create('clinical_impression_problems', static function (Blueprint $table) {
            $table->comment('relevant impressions of patient state');
            $table->id();
            $table->foreignId('clinical_impression_id')->constrained()->cascadeOnDelete();
            $table->foreignId('identifier_id')->constrained('identifiers')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('clinical_impression_findings', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinical_impression_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_reference_id')->constrained('identifiers')->cascadeOnDelete();
            $table->text('basis')->nullable()->comment('Some basis');
            $table->timestamps();
        });

        Schema::create('clinical_impression_supporting_info', static function (Blueprint $table) {
            $table->id();
            $table->foreignId('clinical_impression_id')->constrained()->cascadeOnDelete();
            $table->foreignId('identifier_id')->constrained('identifiers')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clinical_impression_supporting_info');

        Schema::dropIfExists('clinical_impression_findings');

        Schema::dropIfExists('clinical_impression_problems');

        Schema::dropIfExists('clinical_impressions');
    }
};
