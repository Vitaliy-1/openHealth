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
            $table->string('name');
            $table->string('short_name');
            $table->string('public_name');
            $table->string('status');
            $table->string('type');
            $table->string('owner_property_type');
            $table->string('legal_form');
            $table->string('edrpou');
            $table->jsonb('kveds');
            $table->jsonb('addresses');
            $table->jsonb('phones');
            $table->string('email');
            $table->boolean('is_active');
            $table->boolean('mis_verified');
            $table->boolean('nhs_verified');
            $table->string('website')->nullable();
            $table->string('beneficiary');
            $table->string('receiver_funds_code');
            $table->jsonb('archive');
            $table->timestamps();
            $table->uuid('inserted_by');
            $table->uuid('updated_by');
            $table->index('email');
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
