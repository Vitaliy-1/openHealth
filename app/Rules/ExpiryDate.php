<?php

/**
 * Checks the birthdate according to the ezdorovya specification: https://e-health-ua.atlassian.net/wiki/spaces/EH/pages/583402887/Create+employee+request+v2
 */

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Carbon;
use Illuminate\Translation\PotentiallyTranslatedString;

class ExpiryDate implements ValidationRule
{
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
        $expirationDate = Carbon::parse($value);

        if ($expirationDate->gte(Carbon::now())) {
            $fail(__('validation.employee.party.birth_date_value'));
        }

        if (!preg_match('/^(\\d{4}(?!\\d{2}\\b))((-?)((0[1-9]|1[0-2])(\\3([12]\\d|0[1-9]|3[01]))?|W([0-4]\\d|5[0-2])(-?[1-7])?|(00[1-9]|0[1-9]\\d|[12]\\d{2}|3([0-5]\\d|6[1-6])))?)?$/u', $value)) {
            $fail(__('validation.employee.birth_date_iso'));
        }
    }
}
