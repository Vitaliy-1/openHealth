<?php

namespace App\Livewire\Contract\Forms;

use App\Rules\CheckDateDifference;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;
use PhpParser\ErrorHandler\Collecting;

class ContractFormRequest extends Form
{

    #[Validate([
        'contractor_payment_details.bank_name' => 'required',
        'contractor_payment_details.MFO' => 'required',
        'contractor_payment_details.payer_account' => 'required',
    ])]
    public ?array $contractor_payment_details = [];

    #[Validate('required|min:10')]
    public ?object  $statute_md5;

    #[Validate('required|min:10')]
    public ?object $additional_document_md5 ;
    #[Validate('required')]
    public ?array $contractor_divisions = [];

    #[Validate('required')]
    public ?string $contractor_base = '';
    #[Validate('required|integer')]
    public string $contractor_rmsp_amount = '';

    #[Validate('required|date')]
    public ?string $start_date = '';

    public ?string $end_date = '';

    #[Validate([
        'external_contractors.legal_entity.name' => 'required',
        'external_contractors.contract.expires_at' => 'required|date',
        'external_contractors.contract.issued_at' => 'required|date',
        'external_contractors.contract.number' => 'required|string',
        'external_contractors.divisions.name' => 'required|string',
        'external_contractors.divisions.medical_service' => 'required|string',
    ])]
    public  ?array $external_contractors = [];

    #[Validate('accepted')]
    public bool $consent_text;


    public string  $previous_request_id = '';

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

    protected function rules()
    {
        return [
            'end_date' => ['required', 'date', 'after_or_equal:start_date',new CheckDateDifference($this->start_date)],
        ];
    }

}
