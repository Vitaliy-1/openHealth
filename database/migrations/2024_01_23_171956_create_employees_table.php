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
            $table->uuid();
            $table->uuid('division_uuid')->nullable();
            $table->uuid('legal_entity_uuid')->nullable();
            $table->string('position');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('employee_type');
            $table->date('inserted_at')->nullable();
            $table->string('status')->nullable();
            $table->foreignId('legal_entity_id')->nullable();
            $table->foreignId('division_id')->nullable();
            $table->foreign('legal_entity_id')->references('id')->on('legal_entities')->onDelete('cascade');
            $table->foreign('division_id')->references('id')->on('divisions')->onDelete('cascade');
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
