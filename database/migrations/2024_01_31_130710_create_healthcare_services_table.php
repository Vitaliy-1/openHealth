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
        Schema::create('healthcare_services', function (Blueprint $table) {
            $table->id();
            $table->uuid()->nullable();
            $table->foreignId('division_id');
            $table->foreign('division_id')->references('id')->on('divisions')->onDelete('cascade');
            $table->string('speciality_type')->nullable();
            $table->string('providing_condition')->nullable();
            $table->string('license_id')->nullable();
            $table->string('status')->nullable();
            $table->jsonb('category');
            $table->jsonb('type')->nullable();
            $table->text('comment')->nullable();
            $table->jsonb('coverage_area')->nullable();
            $table->jsonb('available_time')->nullable();
            $table->jsonb('not_available')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('healthcare_services');
    }
};
