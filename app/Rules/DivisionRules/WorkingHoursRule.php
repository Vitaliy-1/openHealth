<?php

namespace App\Rules\DivisionRules;

use Illuminate\Contracts\Validation\ValidationRule;
use App\Exceptions\CustomValidationException;
use App\Traits\WorkTimeUtilities;
use Carbon\Carbon;
use Closure;

class WorkingHoursRule implements ValidationRule
{
    use WorkTimeUtilities;

    protected array $division;

    protected string $message;

    public function __construct(array $division)
    {
        $this->division = $division;

        $this->message = __('validation.attributes.healthcareService.error.division.workingHours.commonError');
    }

    /**
     * Check that working hours schedule is correct
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $workingHours = $this->division['working_hours'];

        foreach ($this->weekdays as $day => $dayName) {
            // If the first value of array is not set or empty this means that day is day off
            if (empty($workingHours[$day][0])) {
                continue;
            }

            // Check if the shifts used for this day
            $shifts = $workingHours[$day];
            // Is the day use the shifts in workdays
            $isShifts = count($shifts) > 1;
            // Time when previous shift is ended
            $prevShiftEnd = '';
            // Human presentation of the day's name
            $dayName = '[' . $this->weekdays[$day] . ' ] ';

            // Check shifts
            foreach ($shifts as $shiftNumber => $shift) {
                $shiftName = $isShifts ? $dayName . '(Зміна ' .  $shiftNumber + 1 . ') ' : $dayName;

                if (!$this->compareTime($dayName, $shift)) {
                    $this->throwError($shiftName);
                }

                if ($isShifts && $shiftNumber > 0 && $this->isShiftIntersected($prevShiftEnd, $shift[0])) {
                    $this->throwError($shiftName);
                }

                $prevShiftEnd = $shift[1];
            }
        }
    }

    protected function throwError(string $shiftName = ''): void
    {
        throw new CustomValidationException($this->message($shiftName), 'custom');
    }

    protected function setMessage(string $message): void
    {
        $this->message = $message;
    }

    protected function message(string $shiftName = ''): string
    {
        return $shiftName . $this->message;
    }

    /**
     * Check if the beginning of the shift start earlier than one's ending
     *
     * @param array $day
     *
     * @return bool
     */
    protected function compareTime(string $day, array $shift): bool
    {
        $startTime = Carbon::createFromFormat('H:i', $shift[0]);
        $endTime = Carbon::createFromFormat('H:i', $shift[1]);

        if ($startTime->gte($endTime)) {
            $this->setMessage(__('validation.attributes.healthcareService.error.division.workingHours.wrongRange')) ;

            return false;
        }

        return true;
    }

    /**
     * Check if the beginning of the upcoming shift starts after previous one's ending
     *
     * @param string $prevShiftEnd      // The time the next shift will start
     * @param string $currShiftStart    // The time the previous shift ended
     *
     * @return bool
     */
    protected function isShiftIntersected(string $prevShiftEnd, string $currShiftStart): bool
    {
        $startTime = Carbon::createFromFormat('H:i', $currShiftStart);
        $endTime = Carbon::createFromFormat('H:i', $prevShiftEnd);

        if ($startTime->lt($endTime)) {
            $this->setMessage(__('validation.attributes.healthcareService.error.division.workingHours.wrongShiftStart'));

            return true;
        }

        return false;
    }
}
