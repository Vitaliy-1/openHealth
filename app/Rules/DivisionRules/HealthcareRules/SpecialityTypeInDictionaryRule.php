<?php

namespace App\Rules\DivisionRules\HealthcareRules;

use Closure;
use App\Models\Division;


class SpecialityTypeInDictionaryRule extends HealthcareRule
{

    public function __construct(Division $division, array $healthcareService)
    {
        parent::__construct($division, $healthcareService);
    }

    /**
     * Run the validation rule. Check that speciality type is a value from SPECIALITY_TYPE dictionary
     *
     * @param  string  $attribute The name of the attribute being validated
     * @param  mixed  $value The value of the attribute being validated
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail The callback to invoke if validation fails
     * @return void
     */
    protected function checkRule($data = null): bool
    {
        $specialityType = $this->healthcareService['speciality_type'] ?? '';
        $dictionary = array_keys(dictionary()->getDictionary('SPECIALITY_TYPE', true)['values']);

        return !in_array($specialityType, $dictionary, true);
    }

    protected function message(): string
    {
        return __('validation.attributes.healthcareService.error.in_dictionary.specialityType');
    }
}
