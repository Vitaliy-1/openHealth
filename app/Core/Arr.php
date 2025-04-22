<?php

namespace App\Core;

use Illuminate\Support\Arr as BaseArr;
use Illuminate\Support\Str;

class Arr extends BaseArr
{
    public static function toSnakeCase(array $array): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $newKey = Str::snake($key);

            if (is_array($value)) {
                $result[$newKey] = self::toSnakeCase($value);
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }

    /**
     * Recursively convert all array or object keys to camelCase.
     */
    public static function toCamelCase(array|object $data): array
    {
        $result = [];

        $array = is_object($data) ? (array) $data : $data;

        foreach ($array as $key => $value) {
            $newKey = is_string($key) ? Str::camel($key) : $key;

            if (is_array($value) || is_object($value)) {
                $result[$newKey] = self::toCamelCase($value);
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }

    public static function snakeKeys(array|object $data): array
    {
        return self::toSnakeCase($data);
    }

//    public static function camelKeys(array|object $data): array
//    {
//        return self::toCamelCase($data);
//    }
}
