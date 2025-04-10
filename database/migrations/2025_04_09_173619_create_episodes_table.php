<?php

declare(strict_types=1);

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
        Schema::create('episodes', static function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique();
            $table->foreignId('encounter_id')->constrained('encounters')->cascadeOnDelete();
            $table->foreignId('episode_type_id')->constrained('codings')->cascadeOnDelete();
            $table->enum('status', ['active', 'closed', 'entered_in_error']);
            $table->string('name');
            $table->foreignId('managing_organization_id')->constrained('identifiers')->cascadeOnDelete();
            $table->foreignId('care_manager_id')->constrained('identifiers')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('episodes');
    }
};
