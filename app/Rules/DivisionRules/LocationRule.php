<?php

namespace App\Rules\DivisionRules;

use Closure;
use App\Models\Division;
use App\Exceptions\CustomValidationException;
use App\Models\LegalEntity;
use Illuminate\Contracts\Validation\ValidationRule;

class LocationRule implements ValidationRule
{
    protected array $division;

    public function __construct(array $division)
    {
        $this->division = $division;
    }

    /**
     * Run the validation rule. Check that location exists in request for legal entity with type PHARMACY
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $localEntityType = auth()->user()->legalEntity->type;
        $hasLocation = $this->division['location']['longitude'] && $this->division['location']['latitude'];

        // CustomValidationException
        if($localEntityType === 'PHARMACY' && !$hasLocation) {
            throw new CustomValidationException($this->message(), 'custom');
        }
    }

    protected function message(): string
    {
        return __('validation.attributes.healthcareService.error.division.location');
    }
}
