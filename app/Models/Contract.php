<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin IdeHelperContract
 */
class Contract extends Model
{
    use HasFactory;

    public $timestamps = false;


    protected $fillable = [
        'uuid',
        'start_date',
        'end_date',
        'status',
        'contractor_legal_entity_id',
        'contractor_owner_id',
        'contractor_payment_details',
        'bank_name',
        'MFO',
        'payer_account',
        'contractor_rmsp_amount',
        'external_contractor_flag',
        'external_contractors',
        'nhs_signer_id',
        'nhs_signer_base',
        'nhs_payment_method',
        'is_active',
        'is_suspended',
        'issue_city',
        'nhs_contract_price',
        'contract_number',
        'contract_request_id',
        'contract_id',
        'status_reason',
        'inserted_by',
        'inserted_at',
        'updated_at',
        'id_form',
        'nhs_signed_date',
        'type',
        'reason',
        'contractor_base',
        'signed_content_location',
        'skip_provision_deactivation',
        'statute_md5',
        'additional_document_md5',
        'legal_entity_id',

    ];

    protected $casts = [
        'contractor_payment_details' => 'array',
        'external_contractors' => 'array',
    ];

    protected $date = [
        'start_date',
        'end_date',
        'inserted_at',
    ];

    public function legalEntity(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(LegalEntity::class, 'legal_entity_id', 'id');
    }
}
