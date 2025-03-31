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
            $table->uuid();
            $table->json('accreditation')->nullable();
            $table->json('archive')->nullable();
            $table->string('beneficiary')->nullable();
            $table->json('edr')->nullable();
            $table->boolean('edr_verified')->nullable();
            $table->string('edrpou')->nullable();
            $table->string('email')->nullable();
            $table->uuid('inserted_by')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('nhs_comment')->nullable();
            $table->boolean('nhs_reviewed')->default(false);
            $table->boolean('nhs_verified')->default(false);
            $table->string('receiver_funds_code')->nullable();
            $table->string('status')->nullable();
            $table->string('type')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->string('client_id')->nullable();
            $table->string('client_secret')->nullable();
            $table->string('website')->nullable();
            $table->timestamp('inserted_at')->nullable();
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
