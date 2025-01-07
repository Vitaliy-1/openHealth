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
            $table->foreignId('person_request_id')->nullable()->constrained();
            $table->jsonb('documents_relationship');
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
