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
            $table->uuid();
            $table->string('version');
            $table->string('last_name');
            $table->string('first_name');
            $table->string('second_name')->nullable();
            $table->date('birth_date');
            $table->string('birth_country');
            $table->string('birth_settlement');
            $table->string('gender');
            $table->string('email')->nullable();
            $table->string('tax_id')->nullable();
            $table->boolean('invalid_tax_id')->default(false);
            $table->date('death_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('documents');
            $table->json('addresses');
            $table->json('phones')->nullable();
            $table->string('secret')->nullable();
            $table->json('emergency_contact');
            $table->json('confidant_person')->nullable();
            $table->boolean('patient_signed')->default(false);
            $table->boolean('process_disclosure_data_consent')->default(false);
            $table->json('authentication_methods')->nullable();
            $table->enum('preferred_way_communication', ['email', 'phone'])->default('email');
            $table->string('merged_ids')->nullable();
            $table->string('status');
            $table->timestamp('inserted_at')->useCurrent();
            $table->string('inserted_by');
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            $table->string('updated_by')->nullable();
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
