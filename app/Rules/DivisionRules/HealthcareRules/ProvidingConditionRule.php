<?php

namespace App\Rules\DivisionRules\HealthcareRules;

use Closure;
use App\Models\Division;


class ProvidingConditionRule extends HealthcareRule
{

    public function __construct(Division $division, array $healthcareService)
    {
        parent::__construct($division, $healthcareService);
    }

    /**
     * Run the validation rule.
     * Check that there is no another record with the same healthcare service, division_id, speciality type and providing condition
     *
     * @param  string  $attribute The name of the attribute being validated
     * @param  mixed  $value The value of the attribute being validated
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail The callback to invoke if validation fails
     * @return void
     */
    protected function checkRule($data = null): bool
    {
        $providingCondition = $this->healthcareService['providing_condition'];
        $specialityType = $this->healthcareService['speciality_type'] ?? '';

        return $this->checkProvidingCondition($providingCondition) && $this->checkSpecialityType($specialityType);
    }

    protected function message(): string
    {
        return __('validation.attributes.healthcareService.error.constraint.providingCondition');
    }
}
