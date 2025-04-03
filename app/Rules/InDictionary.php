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
     * @param  string  $dictionaryName  The name of the examined dictionary
     */
    public function __construct(protected string $dictionaryName = '')
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
        $dictionary = array_keys(dictionary()->getDictionary($this->dictionaryName)) ?? [];

        // Check if the field value belongs to appropriate dictionary
        if (!in_array($value, $dictionary, true)) {
            $fail(__('Недопустиме значення'));
        }
    }
}
