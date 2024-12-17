<?php

namespace App\Livewire\Division\Forms;

use App\Exceptions\CustomValidationException;
use App\Models\Division;
use App\Rules\DivisionRules\DivisionStatusRule;
use App\Rules\DivisionRules\LegalEntityStatusRule;
use App\Rules\DivisionRules\LocationRule;
use Livewire\Attributes\Validate;
use Livewire\Features\SupportFormObjects\Form;

class DivisionFormRequest extends Form
{
    #[Validate([
        'division.name' => 'required|min:6|max:255',
        'division.type' => 'required',
        'division.email' => 'required|email',
        'division.phones.number' => 'required|string',
        'division.phones.type' => 'required',
        'division.addresses' => 'required',
    ])]
    public ?array $division = [];

    public ?array $addresses = [];

    public function getDivision(): array
    {
        return $this->division;
    }

    public function updateDivision()
    {
        dd('In update addresses');
    }

    public function setDivision(array $division)
    {
        $this->division = $division;
    }

    public function getDivisionParam(string $param): mixed
    {
        return $this->division[$param] ?? '';
    }

    public function setDivisionParam(string $param, mixed $value): void
    {
        $this->division[$param] = $value;
    }

    public function isDivisionParamExistAndNull(string $paramName): bool
    {
        return array_key_exists($paramName, $this->division) && !$this->division[$paramName];
    }

    public function unsetDivisionParam(string $paramName)
    {
        unset($this->division[$paramName]);
    }

    /**
     * TODO: add rule for next cases:
     *
     */
    protected function customRules(string $mode)
    {
        $validationRules = [];

        $commonValidationRules = [
            // Here creating new Division instance needs for compatibility purpose only
            new LegalEntityStatusRule(),
            new LocationRule($this->division)
        ];

        // $timeValidationRules = [
        //     new AvailableTimeRule($division, $this->healthcare_service),
        //     new NotAvailableTimeRule($division, $this->healthcare_service)
        // ];

        // $storeValidationRules = [
        //     new CategoryInDictionaryRule($division, $this->healthcare_service),
        //     new SpecialityTypeInDictionaryRule($division, $this->healthcare_service),
        //     new CategoryInPharmacyRule($division),
        //     new CategoryRule($division, $this->healthcare_service),
        //     new ProvidingConditionRule($division, $this->healthcare_service),
        // ];

        if ($mode === 'edit') {
            $validationRules = array_merge($validationRules, $commonValidationRules);
        } else {
            $validationRules = array_merge($validationRules, $commonValidationRules);
        }

        return $validationRules;
    }

    protected function customRulesValidation(string $mode): string
    {
        foreach ($this->customRules($mode) as $rule) {
            try {
                $rule->validate('', '', fn() => null);
            } catch (CustomValidationException $e) {
                return $e->getMessage();
            }
        }

        return '';
    }

    /**
     * Do form's validation (correctness of filling the form fields)
     *
     * @return mixed
     */
    public function doValidation(string $mode)
    {
        $this->resetErrorBag();

        $this->validate();

        $failMessage = $this->customRulesValidation($mode);

        return $failMessage;
    }

    public function rules()
    {
        return [
            'division.external_id' => 'nullable|integer|gt:0',
            'division.location.longitude' => 'nullable|numeric|required_with:division.location.latitude',
            'division.location.latitude' => 'nullable|numeric|required_with:division.location.longitude'
        ];
    }

    public function messages()
    {
        return [
            'division.location.longitude.required_with' => __('Якщо введено широту, довгота також обов’язкова'),
            'division.location.latitude.required_with' => __('Якщо введено довготу, широта також обов’язкова'),
            'division.external_id.integer' => __("Поле 'Зовнішній ідeнтифікатор' має містити ціле число"),
            'division.email.required' => __('Поле E-mail є обов’язковим'),
            'division.email.email' => __('Введіть дійсну адресу електронної пошти'),
            'division.phones.type' => __("Поле 'Тип номера' є обов’язковим"),
            'division.phones.number' => __("Поле 'Номер телефону' є обов’язковим")
        ];
    }

}
