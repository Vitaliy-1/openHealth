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
        Schema::create('declarations', function (Blueprint $table) {
            $table->id();
            $table->uuid();
            $table->string('declaration_number')->unique();
            $table->date('start_date');
            $table->date('end_date');
            $table->dateTime('signed_at');
            $table->jsonb('person')->nullable();
            $table->jsonb('employee')->nullable();
            $table->jsonb('division')->nullable();
            $table->jsonb('legal_entity')->nullable();
            $table->string('status');
            $table->string('scope');
            $table->uuid('declaration_request_id');
            $table->dateTime('inserted_at')->nullable();
            $table->dateTime('updated_at')->nullable();
            $table->string('reason')->nullable();
            $table->string('reason_description')->nullable();
            $table->foreignId('employee_id');
            $table->foreignId('division_id')->nullable();
            $table->foreignId('person_id')->nullable()->constrained('persons')->onDelete('set null');
            $table->foreign('employee_id')->references('id')->on('employees');
            $table->foreign('division_id')->references('id')->on('divisions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('declarations');
    }
};
