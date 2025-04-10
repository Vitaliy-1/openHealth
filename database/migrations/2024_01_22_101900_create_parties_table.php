<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('parties', function (Blueprint $table) {
            $table->id();
            $table->uuid()->nullable();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('second_name')->nullable();
            $table->string('email')->nullable()->index();
            $table->date('birth_date')->nullable();
            $table->string('gender')->nullable();
            $table->string('tax_id')->nullable()->index();
            $table->boolean('no_tax_id')->nullable()->default(false);
            $table->text('about_myself')->nullable();
            $table->integer('working_experience')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parties');
    }
};
