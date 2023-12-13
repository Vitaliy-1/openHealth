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
        Schema::create('persons', function (Blueprint $table) {
            $table->id();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('second_name')->nullable();
            $table->date('birth_date');
            $table->string('birth_country');
            $table->string('birth_settlement');
            $table->string('gender');
            $table->string('email')->nullable();
            $table->string('tax_id')->nullable();
            $table->boolean('invalid_tax_id')->nullable();
            $table->date('death_date')->nullable();
            $table->boolean('is_active');
            $table->jsonb('documents');
            $table->jsonb('addresses');
            $table->jsonb('phones')->nullable();
            $table->string('secret')->nullable();
            $table->jsonb('emergency_contact');
            $table->jsonb('confidant_person')->nullable();
            $table->boolean('patient_signed');
            $table->boolean('process_disclosure_data_consent');
            $table->jsonb('authentication_methods');
            $table->string('preferred_way_communication');
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::dropIfExists('persons');

    }
};
