<?php

use App\Classes\eHealth\Services\SchemaService;
use App\Services\DictionaryService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;

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

if (!function_exists("get_day_name")) {
    function get_day_name($k)
    {
        foreach (all_day() as $day) {
            if ($k === $day['key']) {
                return $day['value'];
            }
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
            } elseif ((empty($value) && $value !== false) || $value === '') {
                unset($array[$key]);
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
                "days_of_week" => checkAndConvertArrayToString(
                    $value["days_of_week"]
                ),
                "all_day" => $value["all_day"],
                "available_start_time" => empty($value["all_day"])
                    ? addSecondsToTime($value["available_start_time"])
                    : '',
                "available_end_time" => empty($value["all_day"])
                    ? addSecondsToTime($value["available_end_time"])
                    : '',
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
                "during" => [
                    "start" => convertToISO8601($value["during"]["start"]),
                    "end" => convertToISO8601($value["during"]["end"]),
                ],
                "description" => $value["description"],
            ];
        }
        return removeEmptyKeys($not_available);
    }
}

if (!function_exists('convertToISO8601')) {
    function convertToISO8601(string $dateString): string
    {
        if (empty($dateString)) {
            return '';
        }

        return CarbonImmutable::parse($dateString)->format('Y-m-d\TH:i:s.v\Z');
    }
}

if (!function_exists('replacePhone')) {
    function removeSpacePhones($phones): array
    {
        return collect($phones)->map(function ($phone) {
            $phone['number'] = '+' . str_replace(' ', '', $phone['number']);
            return $phone;
        })->toArray();
    }
}

if (!function_exists('hisBirthDate')) {
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

if (!function_exists('schemaService')) {
    function schemaService(): SchemaService
    {
        return app(SchemaService::class);
    }
}

if (!function_exists('dictionary')) {
    function dictionary(): DictionaryService
    {
        return app(DictionaryService::class);
    }
}

if (!function_exists('arrayKeysToCamel')) {
    function arrayKeysToCamel(array $array): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $camelKey = Str::camel($key);

            // If value is an array, recursively convert its keys
            $result[$camelKey] = is_array($value)
                ? arrayKeysToCamel($value)
                : $value;
        }

        return $result;
    }
}

if (!function_exists('arrayKeysToSnake')) {
    function arrayKeysToSnake(array $array): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $snakeKey = Str::snake($key);

            // If value is an array, recursively convert its keys
            $result[$snakeKey] = is_array($value)
                ? arrayKeysToSnake($value)
                : $value;
        }

        return $result;
    }
}
