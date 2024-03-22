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
            $table->uuid('uuid');
            $table->uuid('division_uuid')->nullable();
            $table->uuid('legal_entity_uuid')->nullable();
            $table->string('position');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('employee_type');
            $table->jsonb('party');
            $table->jsonb('doctor');
            $table->date('inserted_at')->nullable();
            $table->string('status')->nullable();
            $table->foreignId('person_id');
            $table->foreignId('legal_entity_id');
            $table->foreignId('division_id')->nullable();
            $table->foreign('person_id')->references('id')->on('persons')->onDelete('set null');
            $table->foreign('legal_entity_id')->references('id')->on('legal_entities');
            $table->foreign('division_id')->references('id')->on('divisions');//Todo: add divisions table
            $table->timestamps();
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
