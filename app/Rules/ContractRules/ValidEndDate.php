<?php

namespace App\Rules\ContractRules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Carbon;

class ValidEndDate implements ValidationRule
{
    protected string $startDate;
    protected int $maxPeriodDays;

    /**
     * Create a new rule instance.
     *
     * @param string $startDate
     */
    public function __construct(string $startDate)
    {
        $this->startDate = $startDate;
        $this->maxPeriodDays = config('ehealth.capitation_contract_max_period_days');
    }

    /**
     * Start validation
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Replace all spaces with empty string
        $endDate = str_replace(' ', '', $value);
        $startDate = str_replace(' ', '', $this->startDate);

        // Validate date format
        $datePattern = '/^(\d{4})(-((0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01])|W([0-4]\d|5[0-2])(-[1-7])?|(00[1-9]|0[1-9]\d|[12]\d{2}|3([0-5]\d|6[1-6]))))?$/';

        if (!preg_match($datePattern, $endDate)) {
            $fail('Атрибут :attribute має бути дійсною датою у форматі ISO 8601.');
            return;
        }

        $start = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        // Validate date range
        if ($end->lessThan($start)) {
            $fail('Дата закінчення повинна бути більше або дорівнювати даті початку.');
            return;
        }

        // Validate period max days
        $periodDays = $end->diffInDays($start);
        if ($periodDays > $this->maxPeriodDays) {
            $fail('Різниця між датами закінчення та початку перевищує дозволені ' . $this->maxPeriodDays . ' днів.');
            return;
        }

        // Validate year range
        $startYear = $start->year;
        $endYear = $end->year;

        if ($endYear > $startYear + 1) {
            $fail('Дата закінчення не повинна бути більше ніж на рік після дати початку.');
        }
    }
}

