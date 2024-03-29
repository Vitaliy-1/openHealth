<?php
if (!function_exists('all_day')) {
    function all_day(): array
    {
        return [
            ['key' => 'mon', 'value' => __('Понеділок')],
            ['key' => 'tue', 'value' => __('Вівторок')],
            ['key' => 'wed', 'value' => __('Середа')],
            ['key' => 'thu', 'value' => __('Четвер')],
            ['key' => 'fri', 'value' => __('П’ятниця')],
            ['key' => 'sat', 'value' => __('Субота')],
            ['key' => 'sun', 'value' => __('Неділя')],
        ];
    }
}

if (!function_exists('get_day_key')) {
    function get_day_key($k): mixed
    {
        $data = all_day();
        if (isset($data[$k]) && $k >= 0) {
            return $data[$k]['key'];
        }
        return '';
    }
}

if (!function_exists('get_day_value')) {
    function get_day_value($k)
    {
        $data = all_day();
        if (isset($data[$k]) && $k >= 0) {
            return $data[$k]['value'];
        }
        return '';
    }
}
