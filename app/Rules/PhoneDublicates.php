<?php

namespace App\Rules;

use Closure;

use App\Exceptions\CustomValidationException;
use Illuminate\Contracts\Validation\ValidationRule;

class PhoneDublicates implements ValidationRule
{
    protected $phones;

    public function __construct($phones = [])
    {
        $this->phones = $phones;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        foreach ($this->phones as $key => $value) {
            $type = $value['type'];

            $typeCount = count(array_filter($this->phones, fn($value) => $value['type'] === $type));

            if ($typeCount > 1) {
                $typeName = dictionary()->getDictionary('PHONE_TYPE')[$type];

                throw new CustomValidationException(__('validation.phone.dublicates') .': [' . $typeName . ']: ' . $value['number'], 'custom');
            }
        }
    }
}
