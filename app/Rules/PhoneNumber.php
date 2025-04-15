<?php

namespace App\Rules;

use Closure;

use Illuminate\Contracts\Validation\ValidationRule;

class PhoneNumber implements ValidationRule
{
    protected $minDigits;

    public function __construct($minDigits = 9)
    {
        $this->minDigits = $minDigits;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!preg_match('/^\+380[0-9]{'.$this->minDigits.'}$/', $value)) {
            $fail(__('validation.phone', ['min' => $this->minDigits]));
        }
    }
}
