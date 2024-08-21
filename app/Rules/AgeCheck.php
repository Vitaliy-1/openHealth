<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Carbon\Carbon;

class AgeCheck implements ValidationRule
{
    protected $minAge;

    /**
     * Create a new rule instance.
     *
     * @param int $minAge The minimum age required
     */
    public function __construct(int $minAge = 18)
    {
        $this->minAge = $minAge;
    }

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute The name of the attribute being validated
     * @param  mixed  $value The value of the attribute being validated
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail The callback to invoke if validation fails
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Attempt to parse the value as a date
        try {
            $dateOfBirth = Carbon::parse($value);
        } catch (\Exception $e) {
            $fail(':attribute is not a valid date format.');
            return;
        }

        // Calculate the age from the date of birth
        $age = $dateOfBirth->age;

        // Check if the age meets the minimum requirement
        if ($age < $this->minAge) {
            $fail(':attribute повина бути не менше ' . $this->minAge . ' років');
        }
    }
}
