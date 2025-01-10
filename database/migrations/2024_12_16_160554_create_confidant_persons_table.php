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
        Schema::create('confidant_persons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->nullable()->constrained('persons');
            $table->foreignId('person_request_id')->constrained();
            $table->jsonb('documents_relationship');
            $table->string('person_uuid');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('second_name')->nullable();
            $table->enum('gender', ['MALE', 'FEMALE']);
            $table->date('birth_date');
            $table->string('birth_country');
            $table->string('birth_settlement');
            $table->string('tax_id')->nullable();
            $table->string('birth_certificate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('confidant_persons');
    }
};
