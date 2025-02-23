<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Name implements ValidationRule
{
    /**
     * Run the validation rule.
     * see: https://e-health-ua.atlassian.net/wiki/spaces/EH/pages/583402887/Create+employee+request+v2
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!preg_match('/^(?!.*[ЫЪЭЁыъэё@%&$^#])[А-ЯҐЇІЄа-яґїіє’\\\'\\- ]+$/u', $value)) {
            $fail(' :attribute має містити лише кириличні символи.');
        }
    }
}
