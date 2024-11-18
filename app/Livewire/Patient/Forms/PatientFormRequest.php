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
        'patient.first_name' => ['required', 'min:3', new Cyrillic()],
        'patient.last_name' => ['required', 'min:3', new Cyrillic()],
        'patient.second_name' => ['nullable', 'min:3', new Cyrillic()],
        'patient.birth_date' => ['required', 'date', new AgeCheck()],
        'patient.birth_country' => ['required', 'string'],
        'patient.birth_settlement' => ['required', 'string'],
        'patient.gender' => ['required', 'string'],
        'patient.tax_id' => ['required', 'string', 'numeric', 'digits:10'],
        'patient.secret' => ['required', 'string', 'min:6'],
        'patient.email' => ['nullable', 'email', 'string'],
        'patient.preferred_way_communication' => ['nullable', 'string'],

        'patient.phones.type' => ['nullable', 'string'],
        'patient.phones.number' => ['nullable', 'string', 'min:13', 'max:13'],

        'patient.emergency_contact.first_name' => ['required', 'min:3', new Cyrillic()],
        'patient.emergency_contact.last_name' => ['required', 'min:3', new Cyrillic()],
        'patient.emergency_contact.second_name' => ['nullable', 'min:3', new Cyrillic()],
        'patient.emergency_contact.phones.type' => ['required', 'string'],
        'patient.emergency_contact.phones.number' => ['required', 'string', 'min:13', 'max:13'],

        'patient.authentication_methods.type' => ['required', 'string'],
        'patient.authentication_methods.phone_number' => ['required', 'string', 'min:13', 'max:13'],
    ])]
    public array $patient = [];

    #[Validate([
        'documents.type' => ['required', 'string'],
        'documents.number' => ['required', 'string', 'max:255'],
        'documents.issued_by' => ['required', 'string'],
        'documents.issued_at' => ['required', 'date', 'before:today'],
        'documents.expiration_date' => ['nullable', 'date', 'after:today'],
        'documents.unzr' => ['nullable', 'string', new Unzr()],
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

    /**
     * @throws ValidationException
     */
    public function rulesForModelValidate(string $model): array
    {
        return $this->validate($this->rulesForModel($model)->toArray());
    }

    public function validateBeforeSendApi(): array
    {

        if (empty($this->patient)) {
            return [
                'error' => true,
                'message' => __('validation.custom.documents_empty'),
            ];
        }

        if (isset($this->patient['tax_id']) && empty($this->patient['tax_id'])) {
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
