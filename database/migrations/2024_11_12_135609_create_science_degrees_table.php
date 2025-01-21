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
                // default index name is too long for mysql
                $table->morphs('science_degreeable', 'sd_type_sd_id_index');
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
