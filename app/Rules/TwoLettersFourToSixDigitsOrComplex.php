<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class TwoLettersFourToSixDigitsOrComplex implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!preg_match('/^(((?![ЫЪЭЁ])([А-ЯҐЇІЄ])){2}[0-9]{4,6}|[0-9]{9}|((?![ЫЪЭЁ])([А-ЯҐЇІЄ])){2}[0-9]{5}\\/[0-9]{5})$/u',
            $value)) {
            $fail(' :attribute має відповідати формату дві літери кирилицею + від 4 до 6 цифр або 9 цифр або або 2 букви, 5 цифр, слеш, і ще 5 цифр.');
        }
    }
}
