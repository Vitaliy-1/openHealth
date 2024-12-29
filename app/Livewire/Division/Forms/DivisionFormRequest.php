<?php

namespace App\Livewire\Division\Forms;

use App\Rules\DivisionRules\LegalEntityStatusRule;
use Livewire\Features\SupportFormObjects\Form;
use App\Rules\DivisionRules\WorkingHoursRule;
use App\Exceptions\CustomValidationException;
use App\Rules\DivisionRules\LocationRule;
use App\Rules\DivisionRules\AddressRule;
use App\Rules\DivisionRules\EmailRule;
use App\Rules\DivisionRules\PhoneRule;
use App\Rules\DivisionRules\TypeRule;
use Livewire\Attributes\Validate;

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

    // It mostly concerns to the 'work_hours' value
    public function isDivisionParamExistAndNull(string $paramName): bool
    {
        return array_key_exists($paramName, $this->division) && !$this->division[$paramName];
    }

    public function unsetDivisionParam(string $paramName)
    {
        unset($this->division[$paramName]);
    }

    protected function customRules()
    {
        return [
            // Check that legal entity is in ‘ACTIVE’ or ‘SUSPENDED’ status
            new LegalEntityStatusRule(),
            // Check that location exists in request for legal entity with type PHARMACY
            new LocationRule($this->division),
            /**
             * // Check that all bunch of the address' data is correct and valid
             * new AddressRule($this->division), // TODO: uncomment after resolve the #110 ISSUE
             */
            // Check that working hours schedule is correct
            new WorkingHoursRule($this->division),
            // Check that phone type exists in dictionaries and valid accordingly to international rules
            new PhoneRule($this->division),
            // Check that Email has a valid format and specified correctly
            new EmailRule($this->division),
            // Check that Division type exists in dictionaries
            new TypeRule($this->division)
        ];
    }

    protected function customRulesValidation(): string
    {
        foreach ($this->customRules() as $rule) {
            try {
                $rule->validate('', '', fn() => null);
            } catch (CustomValidationException $e) {
                return $e->getMessage();
            }
        }

        return '';
    }

    /**
     * Do form's validation (check correctness of filling the form fields)
     *
     * @return mixed
     */
    public function doValidation()
    {
        $this->resetErrorBag();

        $this->validate();

        $failMessage = $this->customRulesValidation();

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
