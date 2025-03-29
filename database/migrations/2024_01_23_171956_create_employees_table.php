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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->uuid('division_uuid')->nullable();
            $table->uuid('legal_entity_uuid')->nullable();
            $table->string('position');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->string('employee_type');
            $table->date('inserted_at')->nullable();
            $table->string('status')->nullable();
            $table->foreignId('legal_entity_id')->nullable()->constrained('legal_entities')->onDelete('cascade');
            $table->foreignId('division_id')->nullable()->constrained('divisions')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('party_id')->nullable()->constrained('parties')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
