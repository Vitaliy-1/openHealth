<?php

namespace App\Core;

class ArrayCaseCaster
{
    /**
     * Recursively transforms all array keys from camelCase to snake_case
     */
    public static function toSnakeCase(array $array): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $newKey = \Illuminate\Support\Str::snake($key);

            if (is_array($value)) {
                $result[$newKey] = self::toSnakeCase($value);
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }

    /**
     * Transforms all array keys from snake_case to camelCase (optional)
     */
    public static function toCamelCase(array $array): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $newKey = \Illuminate\Support\Str::camel($key);

            if (is_array($value)) {
                $result[$newKey] = self::toCamelCase($value);
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }
}
