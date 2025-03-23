<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;


class Email implements ValidationRule
{
    /**
     * Check that Email has a valid format and specified correctly
     *
     * @param  \Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!preg_match("/^[\w!#$%&'*+\/=?`{|}~^-]+(?:\.[\w!#$%&'*+\/=?`{|}~^-]+)*@(?:[a-z0-9-]+\.)+[a-z]{2,}$/i", $value)) {
            $fail(__('validation.attributes.errors.email'));
        }
    }
}
