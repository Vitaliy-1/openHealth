<?php

use App\Helpers\JsonHelper;
use Carbon\Carbon;

if (!function_exists("all_day")) {
    function all_day(): array
    {
        return [
            ["key" => "mon", "value" => __("Понеділок")],
            ["key" => "tue", "value" => __("Вівторок")],
            ["key" => "wed", "value" => __("Середа")],
            ["key" => "thu", "value" => __("Четвер")],
            ["key" => "fri", "value" => __("П’ятниця")],
            ["key" => "sat", "value" => __("Субота")],
            ["key" => "sun", "value" => __("Неділя")],
        ];
    }
}

if (!function_exists("get_day_key")) {
    function get_day_key($k): mixed
    {
        $data = all_day();
        if (isset($data[$k]) && $k >= 0) {
            return $data[$k]["key"];
        }
        return "";
    }
}

if (!function_exists("get_day_value")) {
    function get_day_value($k)
    {
        $data = all_day();
        if (isset($data[$k]) && $k >= 0) {
            return $data[$k]["value"];
        }
        return "";
    }
}

if (!function_exists("removeEmptyKeys")) {
    function removeEmptyKeys(array $array): array
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = removeEmptyKeys($value);
                if (empty($array[$key])) {
                    unset($array[$key]);
                }
            } else {
                if (empty($value) && $value !== false) {
                    unset($array[$key]);
                }
            }
        }
        return $array;
    }
}

if (!function_exists("available_time")) {
    function available_time($available_times): array
    {
        $available_time = [];
        foreach ($available_times as $key => $value) {
            $available_time[] = [
                "days_of_week"         => checkAndConvertArrayToString(
                    $value["days_of_week"]
                ),
                "all_day"              => $value["all_day"],
                "available_start_time" => addSecondsToTime(
                    $value["available_start_time"]
                ),
                "available_end_time"   => addSecondsToTime(
                    $value["available_end_time"]
                ),
            ];
        }

        return removeEmptyKeys($available_time);
    }
}

if (!function_exists("checkAndConvertArrayToString")) {
    function checkAndConvertArrayToString($value): array
    {
        if (!is_array($value)) {
            return [$value];
        }
        return $value;
    }
}

if (!function_exists("addSecondsToTime")) {
    function addSecondsToTime($timeString, $seconds = 0)
    {
        $dateTime = DateTime::createFromFormat("H:i", $timeString);
        if ($dateTime === false) {
            // Если формат 'H:i' не подходит, попробуем 'H:i:s'
            $dateTime = DateTime::createFromFormat("H:i:s", $timeString);
        }
        if ($dateTime !== false) {
            $dateTime->modify("+{$seconds} seconds");
            return $dateTime->format("H:i:s");
        }
        return $timeString; // Возврат исходного времени, если формат неверен
    }
}

if (!function_exists("not_available")) {
    function not_available($not_availables, $seconds = 0)
    {
        $not_available = [];

        foreach ($not_availables as $key => $value) {
            $not_available[] = [
                "during"      => [
                    "start" => convertToISO8601($value["during"]["start"]),
                    "end"   => convertToISO8601($value["during"]["end"]),
                ],
                "description" => $value["description"],
            ];
        }
        return removeEmptyKeys($not_available);
    }

    if (!function_exists("convertToISO8601")) {
        function convertToISO8601($dateString): string
        {
            if (empty($dateString)) {
                return '';
            }
            $dateTime = Carbon::parse($dateString);
            return $dateTime->format("Y-m-d\TH:i:s.v\Z"); // Используем .v для миллисекунд
        }
    }

}
if (!function_exists('replacePhone')) {
    function removeSpacePhones($phones): array
    {
        return collect($phones)->map(function ($phone) {
            $phone['number'] ='+'. str_replace(' ', '', $phone['number']);
            return $phone;
        })->toArray();

    }
}

if (!function_exists('hisBirthDate')){
     function humanFormatDate($data = ''): string
    {
        // Check if 'person' and 'birth_date' exist
        if (isset($data) && !empty($data)) {
            // Use Carbon to create a date object from the string
            $date = \Illuminate\Support\Carbon::parse($data);
            // Format the date and return it
            return $date->translatedFormat('j F Y');
        }
        // Return an empty string if the birth_date is missing or invalid
        return '';
    }
}
