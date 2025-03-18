<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class InDictionaryCheck implements ValidationRule
{
    /**
     * Create a new rule instance.
     *
     * @param  string  $field  The type associated with the dictionary
     */
    public function __construct(protected string $field = '')
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
        $dictionary = $this->getDictionaryData($this->field);

        // Check if the field value belongs to appropriate dictionary
        if (!in_array($value, $dictionary)) {
            $fail(__('Недопустиме значення'));
        }
    }

    /**
     * Get the array of valid values from the dictionary for a specific field.
     *
     * @param string $field The field name to look up in the dictionary
     *
     * @return array An array containing valid values from the dictionary for the given field
     */
    protected function getDictionaryData(string $field): array
    {
        return match($field) {
            'phone_type' => array_keys(dictionary()->getDictionary('PHONE_TYPE', true)),
            'position' => array_keys(dictionary()->getDictionary('POSITION', true)),
            'document_type' => array_keys(dictionary()->getDictionary('DOCUMENT_TYPE', true)),
            default => []
        };
    }
}
