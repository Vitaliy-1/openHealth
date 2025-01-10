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
        Schema::create('authentication_methods', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['THIRD_PERSON', 'OTP', 'OFFLINE', 'NA']);
            $table->string('phone_number')->nullable();
            $table->string('value')->nullable();
            $table->string('alias')->nullable();
            $table->morphs('authenticatable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authentication_methods');
    }
};
