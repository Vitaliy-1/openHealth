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
        Schema::create('specialities', function (Blueprint $table) {
            $table->id();
            $table->string('speciality');
            $table->boolean('speciality_officio');
            $table->string('level');
            $table->string('qualification_type');
            $table->string('attestation_name');
            $table->date('attestation_date');
            $table->date('valid_to_date')->nullable();
            $table->string('certificate_number');
            $table->morphs('specialityable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('specialities');
    }
};
