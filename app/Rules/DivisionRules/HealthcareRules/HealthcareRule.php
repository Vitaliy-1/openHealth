<?php

namespace App\Rules\DivisionRules\HealthcareRules;

use Illuminate\Contracts\Validation\ValidationRule;
use App\Exceptions\CustomValidationException;
use App\Models\Division;
use Closure;

abstract class HealthcareRule implements ValidationRule
{
    protected array $healthcareService;

    protected Division $division;

    public function __construct(Division $division, array $healthcareService)
    {
        $this->division = $division;
        $this->healthcareService = $healthcareService;
    }

    /**
     * Run the validation rule. Common checking routine.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // CustomValidationException
        if($this->checkRule()) {
            throw new CustomValidationException($this->message(), 'custom');
        }
    }

    abstract protected function checkRule($data = null): bool;

    abstract protected function message(): string;

    /**
     * Check out of uniqueness of providing condition with specified providing_condition type for current Division and HealthcareService
     *
     * @param string $providingCondition
     *
     * @return bool
     */
    protected function checkProvidingCondition(string $providingCondition): bool
    {
        return $this->division->healthcareService()->where('providing_condition', $providingCondition)->get()->count() > 0;
    }

    /**
     * Check out of uniqueness of categories with specified category name for current Division and HealthcareService
     *
     * @param string $categoryName
     *
     * @return bool
     */
    protected function checkCategory(string $categoryName): bool
    {
        return $this->division->healthcareService()->whereJsonContains('category->coding[0]->code', $categoryName)->count() > 0;
    }

    /**
     * Check out of uniqueness of speciality types matches with specified speciality_type name for current Division and HealthcareService
     *
     * @param string $specialityType
     *
     * @return bool
     */
    protected function checkSpecialityType(string $specialityType): bool
    {
        return $this->division->healthcareService()->where('speciality_type', $specialityType)->get()->count() > 0;
    }
}
