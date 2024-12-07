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
            Schema::create('science_degrees', function (Blueprint $table) {
                $table->id();
                $table->string('country');
                $table->string('city');
                $table->string('institution_name');
                $table->string('degree');
                $table->string('diploma_number');
                $table->string('speciality');
                $table->date('issued_date')->nullable();
                $table->morphs('science_degreeable');
                $table->timestamps();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('science_degrees');
    }
};
