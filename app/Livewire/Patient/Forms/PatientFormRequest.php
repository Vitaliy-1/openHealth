<?php

namespace App\Livewire\Patient\Forms;

use App\Rules\AgeCheck;
use App\Rules\Cyrillic;
use App\Rules\Unzr;
use App\Rules\Zip;
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
    ])]
    public ?array $patient = [];

    #[Validate([
        'documents.type' => ['required', 'string'],
        'documents.number' => ['required', 'string', 'max:255'],
        'documents.issued_by' => ['required', 'string'],
        'documents.issued_at' => ['required', 'date', 'before:today'],
        'documents.expiration_date' => ['nullable', 'date', 'after:today'],
        'documents.unzr' => ['nullable', 'string', new Unzr()],
    ])]
    public ?array $documents = [];

    #[Validate([
        'identity.tax_id' => ['required', 'string', 'numeric', 'digits:10'],
    ])]
    public ?array $identity = [];

    #[Validate([
        'contact_data.phones.*.type' => ['nullable', 'string'],
        'contact_data.phones.*.number' => ['nullable', 'string', 'min:13', 'max:13'],
        'contact_data.email' => ['nullable', 'email', 'string'],
        'contact_data.preferred_way_communication' => ['nullable', 'string'],
        'contact_data.secret' => ['required', 'string', 'min:6'],
    ])]
    public ?array $contact_data = [];

    #[Validate([
        'emergency_contact.first_name' => ['required', 'min:3', new Cyrillic()],
        'emergency_contact.last_name' => ['required', 'min:3', new Cyrillic()],
        'emergency_contact.second_name' => ['nullable', 'min:3', new Cyrillic()],
        'emergency_contact.phones.type' => ['required', 'string'],
        'emergency_contact.phones.number' => ['required', 'string', 'min:13', 'max:13'],
    ])]
    public ?array $emergency_contact = [];

    #[Validate([
        'addresses.area' => ['required', 'string'],
        'addresses.settlement' => ['required', 'string'],
        'addresses.street_type' => ['required', 'string'],
        'addresses.street' => ['required', 'string'],
        'addresses.building' => ['required', 'string'],
        'addresses.apartment' => ['required', 'string'],
        'addresses.zip' => ['nullable', 'string', new Zip()],
    ])]
    public ?array $addresses = [];

    #[Validate([
        'confidant_person.relation_type' => ['required', 'string'],
        'confidant_person.first_name' => ['required', 'min:3', new Cyrillic()],
        'confidant_person.last_name' => ['required', 'min:3', new Cyrillic()],
        'confidant_person.second_name' => ['nullable', 'min:3', new Cyrillic()],
        'confidant_person.birth_date' => ['required', 'date', new AgeCheck()],
        'confidant_person.birth_country' => ['required', 'string'],
        'confidant_person.birth_settlement' => ['required', 'string'],
        'confidant_person.tax_id' => ['nullable', 'string', 'numeric', 'digits:10'],
        'confidant_person.unzr' => ['nullable', 'string', new Unzr()],
    ])]
    public ?array $confidant_person = [];

    #[Validate([
        'confidant_person_documents.type' => ['required', 'string'],
        'confidant_person_documents.number' => ['required', 'string'],
        'confidant_person_documents.issued_by' => ['required', 'string'],
        'confidant_person_documents.issued_at' => ['required', 'date'],
        'confidant_person_documents.expiration_date' => ['nullable', 'date'],
    ])]
    public ?array $confidant_person_documents = [];

    #[Validate([
        'confidant_person_documents_relationship.type' => ['required', 'string'],
        'confidant_person_documents_relationship.number' => ['required', 'string'],
        'confidant_person_documents_relationship.issued_by' => ['required', 'string'],
        'confidant_person_documents_relationship.issued_at' => ['required', 'date'],
    ])]
    public ?array $confidant_person_documents_relationship = [];

    #[Validate([
        'legal_representation_contact.phones.*.type' => ['nullable', 'string'],
        'legal_representation_contact.phones.*.number' => ['nullable', 'string', 'min:13', 'max:13'],
        'legal_representation_contact.email' => ['nullable', 'email', 'string'],
        'legal_representation_contact.preferred_way_communication' => ['nullable', 'string'],
    ])]
    public ?array $legal_representation_contact = [];

    #[Validate([
        'authentication_methods.type' => ['required', 'string'],
        'authentication_methods.phone_number' => ['required', 'string', 'min:13', 'max:13'],
    ])]
    public ?array $authentication_methods = [];

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
