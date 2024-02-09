<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('koatuu_level3', function (Blueprint $table) {
            $table->char('id', 10)->primary();
            $table->integer('type');
            $table->char('koatuu_level2_id', 4);
            $table->integer('koatuu_level2_type');
            $table->char('koatuu_level1_id', 2);
            $table->string('name');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('koatuu_level3');
    }
};