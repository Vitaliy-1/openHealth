<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidIBAN implements ValidationRule
{
    /**
     * Start validation
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        //replace all spaces with empty string
        $iban = str_replace(' ', '', $value);
        //check if iban is valid regex for iban UA
        if (!preg_match('/^UA\d{22}$|^UA\d{27}$/', $iban)) {
            $fail('Атрибут :attribute має бути дійсним IBAN України з 22 або 27 цифрами після "UA".');
        }
    }
}
