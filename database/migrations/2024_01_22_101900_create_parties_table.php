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
            $table->uuid();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('second_name')->nullable();
            $table->uuid('person_id')->nullable();
            $table->string('email')->nullable();
            $table->date('birth_date');
            $table->string('gender');
            $table->string('tax_id');
            $table->boolean('no_tax_id')->default(false);
            $table->jsonb('documents');
            $table->jsonb('phones');
            $table->jsonb('educations')->nullable();
            $table->jsonb('qualifications')->nullable();
            $table->jsonb('specialities')->nullable();
            $table->jsonb('science_degree')->nullable();
            $table->string('about_myself')->nullable();
            $table->string('working_experience')->nullable();
            $table->timestamp('inserted_at')->useCurrent();
            $table->uuid('inserted_by');
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            $table->uuid('updated_by')->nullable();
            $table->index('email');
            $table->index('tax_id');
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
