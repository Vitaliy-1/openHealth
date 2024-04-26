<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractsTable extends Migration
{
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->uuid('contractor_legal_entity_id');
            $table->uuid('contractor_owner_id');
            $table->string('contractor_base');
            $table->jsonb('contractor_payment_details');
            $table->string('contractor_rmsp_amount');
            $table->boolean('external_contractor_flag')->default(false);
            $table->jsonb('external_contractors')->nullable();
            $table->jsonb('contractor_employee_divisions')->nullable();
            $table->jsonb('contractor_divisions')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->uuid('nhs_legal_entity_id')->nullable();
            $table->uuid('nhs_signer_id')->nullable();
            $table->string('nhs_signer_base');
            $table->uuid('assignee_id')->nullable();
            $table->string('issue_city');
            $table->string('status');
            $table->string('status_reason');
            $table->double('nhs_contract_price');
            $table->string('nhs_payment_method');
            $table->date('nhs_signed_date')->nullable();
            $table->uuid('previous_request_id')->nullable();
            $table->string('contract_number');
            $table->uuid('contract_id');
            $table->jsonb('data')->nullable();
            $table->string('id_form')->nullable();
            $table->uuid('inserted_by')->nullable();
            $table->timestamp('inserted_at');
            $table->uuid('updated_by')->nullable();
            $table->timestamp('updated_at');
            $table->jsonb('medical_programs')->nullable();
            $table->string('type')->nullable();
            $table->boolean('contractor_signed')->default(false);
            $table->text('misc')->nullable();
            $table->string('statute_md5')->nullable();
            $table->string('additional_document_md5')->nullable();

            $table->foreignId('legal_entity_id')->constrained('legal_entities');

        });
    }

    public function down()
    {
        Schema::dropIfExists('contracts');
    }
}

