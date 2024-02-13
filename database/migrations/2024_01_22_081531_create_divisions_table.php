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
        Schema::create('divisions', function (Blueprint $table) {
                $table->id();
                $table->uuid('uuid')->nullable();
                $table->string('external_id')->nullable();
                $table->string('name');
                $table->string('type')->nullable();
                $table->boolean('mountaint_group');
                $table->jsonb('location');
                $table->jsonb('addresses');
                $table->jsonb('phones');
                $table->string('email');
                $table->jsonb('working_hours')->nullable();
                $table->boolean('is_active')->default(false);
                $table->uuid('legal_entity_uuid')->nullable();
                $table->foreignId('legal_entity_id');
                $table->foreign('legal_entity_id')->references('id')->on('legal_entities');
                $table->string('status')->nullable();
                $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('divisions');
    }
};
