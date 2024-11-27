<?php

namespace App\Rules\DivisionRules\HealthcareRules;

use App\Models\Division;
use Carbon\Carbon;

class AvailableTimeRule extends HealthcareRule
{
    protected string $wrongTimeMessage;

    public function __construct(Division $division, array $healthcareService)
    {
        parent::__construct($division, $healthcareService);

        $this->wrongTimeMessage = __('validation.attributes.healthcareService.error.time.available');
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
        if (empty($this->healthcareService['available_time'])) {
            return false;
        }

        foreach ($this->healthcareService['available_time'] as $dayTime) {
            if (empty($dayTime['all_day'])) {
                // If check fail that Rule must return true as flag that it has been triggered
                if (!$this->checkTime($dayTime)) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function message(): string
    {
        return $this->wrongTimeMessage;
    }

    protected function checkTime(array $day): bool
    {
        $dayOfWeek = $day['days_of_week'];

        $startTime = Carbon::createFromFormat('H:i', $day['available_start_time']);
        $endTime = Carbon::createFromFormat('H:i', $day['available_end_time']);

        if ($startTime->gte($endTime)) {
            $this->wrongTimeMessage = '[' . __(get_day_name($dayOfWeek)) .'] ' . $this->wrongTimeMessage ;

            return false;
        }

        return true;
    }
}
