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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('type')->comment('Dictionary ADDRESS_TYPE');
            $table->string('country')->comment('Dictionary COUNTRY');
            $table->string('area')->comment('one of Ukrainian area');
            $table->string('region')->nullable()->comment('district of area');
            $table->string('settlement')->comment('city name');
            $table->string('settlement_type')->comment('Dictionary SETTLEMENT_TYPE - type of settlement as city/town/village etc');
            $table->string('settlement_id')->comment('settlement identification from uaadresses');
            $table->string('street_type')->nullable()->comment('Dictionary STREET_TYPE - type of street as street/road/line etc');
            $table->string('street')->nullable()->comment('street name');
            $table->string('building')->nullable()->comment('number of building');
            $table->string('apartment')->nullable()->comment('number of apartment');
            $table->string('zip')->nullable()->comment('system of postal codes');
            $table->morphs('addressable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
