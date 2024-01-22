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
        Schema::create('legal_entities', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->json('addresses');
            $table->json('archive')->nullable();
            $table->string('beneficiary')->nullable();
            $table->string('edrpou');
            $table->string('email');
            $table->boolean('is_active');
            $table->string('kveds');
            $table->string('legal_form');
            $table->boolean('mis_verified')->nullable();
            $table->string('name');
            $table->boolean('nhs_verified')->nullable();
            $table->string('owner_property_type');
            $table->json('phones');
            $table->string('public_name');
            $table->json('license')->nullable();
            $table->json('accreditation')->nullable();
            $table->string('receiver_funds_code')->nullable();
            $table->string('short_name');
            $table->string('status');
            $table->string('type');
            $table->string('website')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('legal_entities');
    }
};
