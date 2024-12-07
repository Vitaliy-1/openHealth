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
        Schema::create('qualifications', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('institution_name');
            $table->string('speciality');
            $table->date('issued_date');
            $table->string('certificate_number');
            $table->date('valid_to')->nullable();
            $table->string('additional_info')->nullable();
            $table->morphs('qualificationable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qualifications');
    }
};
