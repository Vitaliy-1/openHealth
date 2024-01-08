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
            $table->uuid('person_uuid')->nullable();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('second_name')->nullable();
            $table->date('birth_date');
            $table->string('birth_country')->nullable();
            $table->string('birth_settlement')->nullable();
            $table->string('gender');
            $table->string('email')->nullable();
            $table->string('tax_id')->nullable();
            $table->boolean('invalid_tax_id')->nullable();
            $table->date('death_date')->nullable();
            $table->boolean('is_active')->default(false);
            $table->jsonb('documents');
            $table->jsonb('addresses')->nullable();
            $table->jsonb('phones')->nullable();
            $table->string('secret')->nullable();
            $table->jsonb('emergency_contact');
            $table->jsonb('confidant_person')->nullable();
            $table->boolean('patient_signed')->default(false);
            $table->boolean('process_disclosure_data_consent')->default(false);
            $table->jsonb('authentication_methods')->nullable();
            $table->string('preferred_way_communication')->nullable();
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
