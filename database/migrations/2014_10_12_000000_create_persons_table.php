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
        Schema::create('persons', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->nullable();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('second_name')->nullable();
            $table->string('email');
            $table->date('birth_date');
            $table->string('gender');
            $table->string('tax_id');
            $table->boolean('no_tax_id')->nullable();
            $table->json('documents');
            $table->json('phones');
            $table->json('educations')->nullable();
            $table->json('qualifications')->nullable();
            $table->json('specialities')->nullable();
            $table->json('science_degree')->nullable();
            $table->text('about_myself')->nullable();
            $table->string('working_experience')->nullable();
            $table->string('declaration_limit')->nullable();
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::dropIfExists('persons');

    }
};
