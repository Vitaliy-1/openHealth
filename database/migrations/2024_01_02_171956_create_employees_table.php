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
//            $table->foreignId('division_id')->nullable();//TODO: divisions table
            $table->uuid('employee_uuid')->nullable();
            $table->foreignId('person_id')->nullable();
            $table->foreignId('legal_entity_id');
            $table->string('position')->nullable();
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
            $table->string('employee_type')->nullable();
            $table->jsonb('party')->nullable();
            $table->jsonb('doctor')->nullable();
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
