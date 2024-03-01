<?php

namespace App\Livewire\Employee\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class EmployeeFormRequest extends Form
{


    #[Validate([
        'passport_data.last_name' => 'required|min:3',
        'passport_data.first_name' => 'required|min:3',
        'passport_data.gender' => 'required|string',
        'passport_data.birth_date' => 'required|date',
        'passport_data.no_tax_id' => 'boolean',
        'passport_data.tax_id' => 'exclude_if:owner.no_tax_id,true|required|integer|digits:10',
        'passport_data.documents.type' => 'exclude_if:owner.no_tax_id,false|required|string',
        'passport_data.documents.number' => 'exclude_if:owner.no_tax_id,false|required|string',
        'passport_data.phones.*.number' => 'required|string:digits:13',
        'passport_data.phones.*.type' => 'required|string',
        'passport_data.email' => 'required|email',
    ])]
    public ?array $passport_data = [];

    public ?array $educations = [];

    public ?array  $specialities = [];

    public ?array  $qualifications = [];

    public ?array $positions = [];

    public ?array $science_degree = [];

    public ?array $role = [];


    public function rulesForModelValidate(string $model): array
    {
        return $this->validate($this->rulesForModel($model)->toArray());
    }


}
