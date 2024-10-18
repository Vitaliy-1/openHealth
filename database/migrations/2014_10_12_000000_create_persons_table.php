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
            $table->string('first_name');
            $table->string('last_name');
            $table->string('second_name')->nullable();
            $table->date('birth_date');
            $table->string('birth_country');
            $table->string('birth_settlement');
            $table->enum('gender', ['male', 'female']);
            $table->string('email')->unique();
            $table->string('tax_id')->nullable();
            $table->string('secret');
            $table->json('documents');
            $table->json('addresses');
            $table->json('phones')->nullable();
            $table->json('authentication_methods');
            $table->enum('preferred_way_communication', ['email', 'phone', 'sms']);
            $table->json('emergency_contact');
            $table->json('confidant_persons');
            $table->boolean('is_active')->default(true);
            $table->json('merged_ids')->nullable();
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
