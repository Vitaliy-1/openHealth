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
        Schema::create('person_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid()->nullable();
            $table->enum('status', ['APPLICATION', 'NEW', 'APPROVED', 'SIGNED', 'REJECTED', 'CANCELLED']);
            $table->foreignId('person_id')->nullable()->constrained('persons');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('second_name')->nullable();
            $table->date('birth_date');
            $table->string('birth_country');
            $table->string('birth_settlement');
            $table->enum('gender', ['MALE', 'FEMALE']);
            $table->string('email')->unique()->nullable();
            $table->boolean('no_tax_id');
            $table->string('tax_id')->nullable();
            $table->string('secret');
            $table->string('unzr')->nullable()->comment('the record number in the demographic register');
            $table->jsonb('emergency_contact');
            $table->boolean('patient_signed')->default(false)->comment("Person's evidence of sign the person request");
            $table->boolean('process_disclosure_data_consent')->default(true)->comment("Person's evidence of information about consent to data disclosure");
            $table->string('authorize_with')->nullable()->comment("identifier of person's auth method");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('person_requests');
    }
};
