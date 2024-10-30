<?php

namespace App\Livewire\Patient\Forms;

use App\Rules\AgeCheck;
use App\Rules\Cyrillic;
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
        'patient.country_of_birth' => ['required', 'string'],
        'patient.city_of_birth' => ['required', 'string'],
        'patient.gender' => ['required', 'string'],
        'patient.tax_id' => ['required', 'string', 'max:10'],
    ])]
    public ?array $patient = [];

    #[Validate([
        'documents.type' => ['required', 'string'],
        'documents.number' => ['required', 'string'],
        'documents.issued_by' => ['required', 'string'],
        'documents.issued_at' => ['required', 'date'],
        'documents.valid_to' => ['nullable', 'integer'],
        'documents.unzr' => ['nullable', 'string'],
    ])]
    public ?array $documents = [];

    /**
     * @throws ValidationException
     */
    public function rulesForModelValidate(string $model): array
    {
        return $this->validate($this->rulesForModel($model)->toArray());
    }
}
