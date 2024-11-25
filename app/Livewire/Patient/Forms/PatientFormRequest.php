<?php

namespace App\Livewire\Patient\Forms;

use App\Rules\AgeCheck;
use App\Rules\Cyrillic;
use App\Rules\Unzr;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class PatientFormRequest extends Form
{
    #[Validate([
        'patient.firstName' => ['required', 'min:3', new Cyrillic()],
        'patient.lastName' => ['required', 'min:3', new Cyrillic()],
        'patient.secondName' => ['nullable', 'min:3', new Cyrillic()],
        'patient.birthDate' => ['required', 'date', new AgeCheck()],
        'patient.birthCountry' => ['required', 'string'],
        'patient.birthSettlement' => ['required', 'string'],
        'patient.gender' => ['required', 'string'],
        'patient.unzr' => ['nullable', 'string', new Unzr()],
        'patient.taxId' => ['required', 'string', 'numeric', 'digits:10'],
        'patient.secret' => ['required', 'string', 'min:6'],
        'patient.email' => ['nullable', 'email', 'string'],
        'patient.preferredWayCommunication' => ['nullable', 'string'],

        'patient.phones.type' => ['nullable', 'string'],
        'patient.phones.number' => ['nullable', 'string', 'min:13', 'max:13'],

        'patient.emergency_contact.firstName' => ['required', 'min:3', new Cyrillic()],
        'patient.emergency_contact.lastName' => ['required', 'min:3', new Cyrillic()],
        'patient.emergency_contact.secondName' => ['nullable', 'min:3', new Cyrillic()],
        'patient.emergency_contact.phones.type' => ['required', 'string'],
        'patient.emergency_contact.phones.number' => ['required', 'string', 'min:13', 'max:13'],

        'patient.authentication_methods.type' => ['required', 'string'],
        'patient.authentication_methods.phoneNumber' => ['required', 'string', 'min:13', 'max:13'],
    ])]
    public array $patient = [];

    #[Validate([
        'documents.type' => ['required', 'string'],
        'documents.number' => ['required', 'string', 'max:255'],
        'documents.issuedBy' => ['required', 'string'],
        'documents.issuedAt' => ['required', 'date', 'before:today', 'after:patient.birthDate'],
        'documents.expirationDate' => ['nullable', 'date', 'after:today'],
    ])]
    public array $documents = [];

    public array $addresses = [];

    #[Validate([
        'documents_relationship.type' => ['required', 'string'],
        'documents_relationship.number' => ['required', 'string'],
        'documents_relationship.issued_by' => ['required', 'string'],
        'documents_relationship.issued_at' => ['required', 'date'],
//        'documents_relationship.active_to' => ['nullable', 'date'],
    ])]
    public array $documents_relationship = [];

    #[Validate([
        'confirmation_code' => ['required', 'numeric', 'digits:4'],
    ])]
    public string $confirmation_code;

    /**
     * Validate data for chosen model
     *
     * @param  string  $model
     * @return array
     * @throws ValidationException
     */
    public function rulesForModelValidate(string $model): array
    {
        $rules = $this->rulesForModel($model)->toArray();

        if ($model === 'documents') {
            $this->addExpirationDateRuleIfRequired($rules);
        }

        return $this->validate($rules);
    }

    /**
     * Do expirationDate required if specific document type was selected
     *
     * @param  array  $rules
     * @return void
     */
    private function addExpirationDateRuleIfRequired(array &$rules): void
    {
        $requiredTypes = [
            'NATIONAL_ID', 'COMPLEMENTARY_PROTECTION_CERTIFICATE', 'PERMANENT_RESIDENCE_PERMIT',
            'REFUGEE_CERTIFICATE', 'TEMPORARY_CERTIFICATE', 'TEMPORARY_PASSPORT'
        ];

        if (empty($this->documents['expirationDate']) && in_array($this->documents['type'], $requiredTypes, true)) {
            $rules['documents.expirationDate'][] = 'required';
        }
    }

    /**
     * Validate data before sending API request
     *
     * @return array
     */
    public function validateBeforeSendApi(): array
    {
        if (empty($this->patient)) {
            return [
                'error' => true,
                'message' => __('validation.custom.documents_empty'),
            ];
        }

        if (isset($this->patient['taxId']) && empty($this->patient['taxId'])) {
            return [
                'error' => true,
                'message' => __('validation.custom.documents_empty'),
            ];
        }

        return [
            'error' => false,
            'message' => '',
        ];
    }
}
