<?php

namespace App\Rules\DivisionRules\HealthcareRules;

use Closure;
use App\Models\Division;
use App\Models\License;

class LicenseRule extends HealthcareRule
{

    public function __construct(Division $division, array $healthcareService)
    {
        parent::__construct($division, $healthcareService);
    }

    /**
     * Run the validation rule.
     * Check that there is any valid license for the healthcare service's category
     *
     * TODO: Need more additional testing rely on the eHealth's Helathcare Service create/update recommendation (Validation category)
     *
     * @param  string  $attribute The name of the attribute being validated
     * @param  mixed  $value The value of the attribute being validated
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail The callback to invoke if validation fails
     *
     * @return void
     */
    protected function checkRule($data = null): bool
    {
        $category = $this->healthcareService['category'];
        $licensesType = License::all('type')->pluck('type')->toArray();

        return !in_array($category, $licensesType, true);
    }

    protected function message(): string
    {
        return __('validation.attributes.healthcareService.error.category.license');
    }
}
