<?php

namespace App\Rules\ContractRules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Carbon;

class ValidStartDate implements ValidationRule
{
    /**
     * Start validation
     *
     * @param string $attribute
     * @param mixed $value
     * @param \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Replace all spaces with empty string
        $datePattern = '/^(\d{4})(-((0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])|W([0-4]\d|5[0-2])(-[1-7])?|(00[1-9]|0[1-9]\d|[12]\d{2}|3([0-5]\d|6[1-6]))))?$/';

        // Validate date format
        if (!preg_match($datePattern, $value)) {
            $fail('Атрибут :attribute має бути дійсною датою у форматі ISO 8601.');
            return;
        }

        $date = Carbon::parse($value);
        $currentYear = Carbon::now()->year;

        $nextYear = $currentYear + 1;
        // Validate date range
        if ($date->year !== $currentYear && $date->year !== $nextYear) {
            $fail('Дата початку повинна бути в межах цього або наступного року.');
        }
    }
}
