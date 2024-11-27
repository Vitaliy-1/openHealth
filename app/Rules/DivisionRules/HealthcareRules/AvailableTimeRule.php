<?php

namespace App\Rules\DivisionRules\HealthcareRules;

use Closure;
use App\Models\Division;


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

    protected function timeToSeconds(string $time): int
    {
        if (empty($time) || !str_contains($time, ':')) {
            return 0;
        }

        $time = explode(':', $time);

        return 3600 * $time[0] + 60 * $time[1];
    }

    protected function checkTime(array $day): bool
    {
        $dayOfWeek = $day['days_of_week'];

        $startTime = $this->timeToSeconds($day['available_start_time']);
        $endTime = $this->timeToSeconds($day['available_end_time']);

        if ($startTime >= $endTime) {
            $this->wrongTimeMessage = '[' . __(get_day_name($dayOfWeek)) .'] ' . $this->wrongTimeMessage ;

            return false;
        }

        return true;
    }
}
