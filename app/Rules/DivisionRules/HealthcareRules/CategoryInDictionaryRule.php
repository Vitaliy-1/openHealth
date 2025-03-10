<?php

namespace App\Rules\DivisionRules\HealthcareRules;

use Closure;
use App\Models\Division;


class CategoryInDictionaryRule extends HealthcareRule
{

    public function __construct(Division $division, array $healthcareService)
    {
        parent::__construct($division, $healthcareService);
    }

    /**
     * Run the validation rule. Check that category is a value from HEALTHCARE_SERVICE_CATEGORIES dictionary
     *
     * @param  string  $attribute The name of the attribute being validated
     * @param  mixed  $value The value of the attribute being validated
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail The callback to invoke if validation fails
     * @return void
     */
    protected function checkRule($data = null): bool
    {
        $category = $this->healthcareService['category'];
        $dictionary = array_keys(dictionary()->getDictionary('HEALTHCARE_SERVICE_CATEGORIES'));

        return !in_array($category, $dictionary, true);
    }

    protected function message(): string
    {
        return __('validation.attributes.healthcareService.error.inDictionary.category');
    }
}
