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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->foreignId('current_team_id')->nullable();
            $table->string('profile_photo_path', 2048)->nullable();
            $table->string('tax_id')->nullable()->unique();
            $table->jsonb('settings')->nullable();
            $table->jsonb('priv_settings')->nullable();
            $table->boolean('is_blocked')->nullable();
            $table->string('block_reason')->nullable();
            $table->foreignId('person_id')->nullable();
            $table->foreign('person_id')->references('id')->on('persons')->onDelete('set null');
            $table->rememberToken();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
