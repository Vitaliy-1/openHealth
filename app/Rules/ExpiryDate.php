<?php

/**
 * Checks the birthdate according to the ezdorovya specification: https://e-health-ua.atlassian.net/wiki/spaces/EH/pages/583402887/Create+employee+request+v2
 */

namespace App\Rules;

use Closure;
use Illuminate\Support\Carbon;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class ExpiryDate implements ValidationRule
{
    protected Carbon|null $startDate;

    /**
     * Check Expiration date.
     * You can pass the start date to check the expiration date against it.
     * If the start date is not passed, the current date will be used.
     *
     * @param array $dates // 'startDate' - the date of start
     */
    public function __construct(string $startDate = '')
    {
        $this->startDate = $startDate ? Carbon::createFromFormat('Y-m-d', $startDate) : null;
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
        // If no value it means that expiry date is not required
        if (!$value) {
            return;
        }

        $expirationDate = Carbon::parse($value);

        if (!empty($this->startDate) && $expirationDate->lte($this->startDate)) {
            $fail(__('validation.attributes.errors.expiryDateLess'));
        }

        if ($expirationDate->lte(Carbon::now())) {
            $fail(__('validation.attributes.errors.expiryDateGreat'));
        }

        if (!preg_match('/^(\\d{4}(?!\\d{2}\\b))((-?)((0[1-9]|1[0-2])(\\3([12]\\d|0[1-9]|3[01]))?|W([0-4]\\d|5[0-2])(-?[1-7])?|(00[1-9]|0[1-9]\\d|[12]\\d{2}|3([0-5]\\d|6[1-6])))?)?$/u', $value)) {
            $fail(__('validation.attributes.errors.date_iso'));
        }
    }
}
