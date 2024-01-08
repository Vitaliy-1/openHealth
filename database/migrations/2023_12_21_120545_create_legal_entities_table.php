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
            $table->uuid('legal_entities_uuid');
            $table->string('name')->nullable();
            $table->string('short_name')->nullable();
            $table->string('public_name');
            $table->string('type')->nullable();
            $table->string('owner_property_type')->nullable();
            $table->string('legal_form')->nullable();
            $table->string('edrpou');
            $table->jsonb('kveds')->nullable();
            $table->jsonb('addresses');
            $table->jsonb('phones');
            $table->string('email');
            $table->boolean('is_active')->default(false);
            $table->string('mis_verified')->nullable();
            $table->boolean('nhs_verified')->default(false);
            $table->string('website')->nullable();
            $table->string('beneficiary')->nullable();
            $table->string('receiver_funds_code');
            $table->jsonb('archive')->nullable();
            $table->jsonb('license')->nullable();
            $table->index('email');
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
