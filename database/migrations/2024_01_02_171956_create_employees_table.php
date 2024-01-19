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
            $table->uuid('uuid')->nullable();
            $table->foreignId('person_id')->nullable();
            $table->string('position');
            $table->string('status');
            $table->string('status_reason')->nullable();
            $table->string('employee');
            $table->string('employee_type');
            $table->boolean('is_active');
            $table->timestamp('start_date');
            $table->timestamp('end_date')->nullable();
            $table->foreignId('legal_entity_id');
//            $table->foreignId('division_id')->nullable();
            $table->json('speciality')->nullable();
            $table->timestamps();
            $table->foreign('person_id')->references('id')->on('persons')->onDelete('set null');
            $table->foreign('legal_entity_id')->references('id')->on('legal_entities');
//            $table->foreign('division_id')->references('id')->on('divisions');//Todo: add divisions table

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
