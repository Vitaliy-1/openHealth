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
            $table->uuid();
            $table->enum('verification_status', ['CHANGES_NEEDED', 'IN_REVIEW', 'NOT_VERIFIED', 'VERIFICATION_NEEDED', 'VERIFICATION_NOT_NEEDED', 'VERIFIED'])->default('IN_REVIEW');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('second_name')->nullable();
            $table->date('birth_date');
            $table->string('birth_country')->nullable();
            $table->string('birth_settlement')->nullable();
            $table->enum('gender', ['MALE', 'FEMALE']);
            $table->string('email')->unique()->nullable();
            $table->boolean('no_tax_id');
            $table->string('tax_id')->unique()->nullable();
            $table->string('secret');
            $table->string('unzr')->unique()->nullable();
            $table->jsonb('emergency_contact');
            $table->boolean('patient_signed')->default(false)->comment("Person's evidence of sign the person request");
            $table->boolean('process_disclosure_data_consent')->default(true)->comment("Person's evidence of information about consent to data disclosure");
            $table->date('death_date')->nullable();
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
