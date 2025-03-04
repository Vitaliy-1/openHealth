<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Carbon\Carbon;
use Illuminate\Translation\PotentiallyTranslatedString;

class AgeCheck implements ValidationRule
{
    /**
     * Create a new rule instance.
     *
     * @param  int  $minAge  The minimum age required
     */
    public function __construct(protected int $minAge = 18)
    {
    }

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute  The name of the attribute being validated
     * @param  mixed  $value  The value of the attribute being validated
     * @param  Closure(string): PotentiallyTranslatedString  $fail  The callback to invoke if validation fails
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Attempt to parse the value as a date
        try {
            $dateOfBirth = Carbon::parse($value);
        } catch (\Exception $e) {
            $fail('Birth date has not a valid date format');

            return;
        }

        // Calculate the age from the date of birth
        $age = $dateOfBirth->age;

        // Check if the age meets the minimum requirement
        if ($age < $this->minAge) {
            $fail('Вік має бути не менше ' . $this->minAge . ' років');
        }
    }
}
