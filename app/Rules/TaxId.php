<?php

/**
 * Checks the birthdate according to the ezdorovya specification: https://e-health-ua.atlassian.net/wiki/spaces/EH/pages/583402887/Create+employee+request+v2
 */

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class TaxId implements ValidationRule
{
    protected bool $noTaxId;

    /**
     * Check taxId value (IPN).
     * You can pass the noTaxId value.
     * If the noTaxId is passed, the document number must match number of the National Passport or the Passport ID.
     *
     * @param array $dates // 'startDate' - the date of start
     */
    public function __construct(bool $noTaxId = false)
    {
        $this->noTaxId = $noTaxId;
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
        if ($this->noTaxId) {
            if (!preg_match('/^([0-9]{9}|[А-ЯЁЇIЄҐ]{2}\\d{6})$/u', $value)) {
                $fail(__('validation.attributes.errors.invalidNationalId'));
            }
        } else {
            if (!preg_match('/^[0-9]{10}$/', $value)) {
                $fail(__('validation.attributes.errors.invalidTaxId'));
            }
        }
    }
}
