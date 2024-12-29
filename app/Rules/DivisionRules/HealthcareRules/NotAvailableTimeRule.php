<?php

namespace App\Rules\DivisionRules\HealthcareRules;

use Closure;
use App\Models\Division;
use Carbon\Carbon;

class NotAvailableTimeRule extends HealthcareRule
{
    protected string $wrongTimeMessage;

    public function __construct(Division $division, array $healthcareService)
    {
        parent::__construct($division, $healthcareService);
    }

    /**
     * Run the validation rule.
     * Check that end time should be greater then start
     *
     * @param  string  $attribute The name of the attribute being validated
     * @param  mixed  $value The value of the attribute being validated
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail The callback to invoke if validation fails
     * @return void
     */
    protected function checkRule($data = null): bool
    {
        if (empty($this->healthcareService['not_available'])) {
            return false;
        }

        foreach ($this->healthcareService['not_available'] as $daysData) {
                // If check fail that Rule must return true as flag that it has been triggered
                if (!$this->checkDate($daysData)) {
                    return true;
            }
        }

        return false;
    }

    protected function message(): string
    {
        return __('validation.attributes.healthcareService.error.time.notAvailable');
    }

    protected function checkDate(array $dayData): bool
    {
        $nonWorking = $dayData['during'];

        $startDate = Carbon::parse($nonWorking['start']);
        $endDate = Carbon::parse($nonWorking['end']);

        if ($startDate->greaterThan($endDate) || $startDate->equalTo($endDate)) {
            return false;
        }

        return true;
    }
}
