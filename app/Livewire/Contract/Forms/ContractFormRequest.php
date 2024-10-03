<?php

namespace App\Livewire\Contract\Forms;

use App\Rules\ContractRules\ValidEndDate;
use App\Rules\ContractRules\ValidStartDate;
use App\Rules\ValidIBAN;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ContractFormRequest extends Form
{

    #[Validate([
        'contractor_payment_details.bank_name'     => 'required|string',
        'contractor_payment_details.payer_account' => ['required', 'string', new ValidIBAN],
        'contractor_payment_details.mfo'           => [
            'required_if:contractor_payment_details.payer_account,!regex:/^UA\d{22}$|^UA\d{27}$/',
            'string',
            'max:6'
        ],
    ])]
    public ?array $contractor_payment_details = [];

    #[Validate('required')]
    public ?object $statute_md5;

    #[Validate('required')]
    public ?object $additional_document_md5;
    #[Validate('required')]
    public ?array $contractor_divisions = [];
    #[Validate('required')]
    public ?string $contractor_base = '';

    public ?string $start_date = '';

    public ?string $end_date = '';

    #[Validate([
        'external_contractors.legal_entity_id'       => 'required',
        'external_contractors.contract.expires_at'       => 'required|date',
        'external_contractors.contract.issued_at'        => 'required|date',
        'external_contractors.contract.number'           => 'required|string',
        'external_contractors.divisions.id'            => 'required|string',
        'external_contractors.divisions.medical_service' => 'required|string',
    ])]
    public ?array $external_contractors = [];

    #[Validate('accepted')]
    public string $consent_text;

    public string $id_form = 'PMD_1';
    public string $previous_request_id = '';

    /**
     * @throws ValidationException
     */
    public function rulesForModelValidate(string $model = ''): array
    {
        $rules = $this->getRules();

        if (empty($model)) {
            $rules = array_filter($rules, function ($key) {
                return strpos($key, 'external_contractors') !== 0;
            }, ARRAY_FILTER_USE_KEY);
            return $this->validate($rules);
        }

        return $this->validate($this->rulesForModel($model)->toArray());
    }

    protected function rules(): array
    {

        return [
            'start_date' => ['required', 'string', new ValidStartDate()],
            'end_date'   => ['required', 'string', new ValidEndDate($this->start_date)],
        ];
    }

}
