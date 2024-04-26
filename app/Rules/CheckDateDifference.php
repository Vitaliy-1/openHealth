<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;

class CheckDateDifference implements Rule
{
    protected string $startDate;

    public function __construct($startDate)
    {
        $this->startDate = !empty($startDate) ? $startDate : Carbon::now()->format('d.m.Y');;
    }

    public function passes($attribute, $value)
    {
        // Преобразование строковых дат в объекты Carbon
        $startDate = Carbon::createFromFormat('d.m.Y', $this->startDate);
        $endDate = Carbon::createFromFormat('d.m.Y', $value);

        // Проверка разницы между датами (в данном случае, больше года)
        return $endDate->diffInDays($startDate) <= 365;
    }

    public function message()
    {
        return __('Дата закінчення не може бути більше 1 року');
    }
}
