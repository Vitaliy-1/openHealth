<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class InDictionary implements ValidationRule
{
    /**
     * Create a new rule instance.
     *
     * @param  string|array  $dictionaryNames  One or multiple dictionary names to check against
     */
    public function __construct(protected string|array $dictionaryNames)
    {
    }

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute  The name of the attribute being validated
     * @param  mixed  $value  The value of the attribute being validated
     * @param  Closure(string): PotentiallyTranslatedString  $fail  The callback to invoke if validation fails
     *
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Normalize dictionary names to array for unified processing
        $names = is_array($this->dictionaryNames)
            ? $this->dictionaryNames
            : [$this->dictionaryNames];

        // A flag to determine if the value exists in at least one dictionary
        $isValid = false;

        foreach ($names as $name) {
            // Retrieve dictionary keys (assumed to be codes)
            $dictionaryKeys = array_keys(dictionary()->getDictionary($name));

            if (in_array($value, $dictionaryKeys, true)) {
                $isValid = true;
                break; // Stop checking once a match is found
            }
        }

        // Fail validation if value not found in any dictionary
        if (! $isValid) {
            $fail(__('Недопустиме значення :attribute'));
        }
    }
}
