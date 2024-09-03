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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->uuid('party_uuid');
            $table->string('position');
            $table->string('email');
            $table->string('status');
            $table->string('status_reason')->nullable();
            $table->string('employee_type');
            $table->boolean('is_active')->default(true);
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->uuid('legal_entity_uui');
            $table->uuid('division_uuid')->nullable();
            $table->jsonb('speciality')->nullable();
            $table->jsonb('properties')->nullable();
            $table->timestamp('inserted_at')->useCurrent();
            $table->uuid('inserted_by');
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            $table->uuid('updated_by')->nullable();
            $table->foreignId('legal_entity_id');
            $table->foreignId('party_id');
            $table->foreignId('division_id');
            $table->foreign('party_id')->references('id')->on('parties');
            $table->foreign('legal_entity_id')->references('id')->on('legal_entities');
            $table->foreign('division_id')->references('id')->on('divisions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
