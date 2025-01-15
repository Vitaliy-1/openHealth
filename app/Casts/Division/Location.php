<?php

namespace App\Casts\Division;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class Location implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        if (!is_array($value)) {
            $data = json_decode($value,true);
        }

        if (!empty($data) && ($data['latitude'] !== 0 || $data['longitude'] !== 0)) {
            $data['latitude'] = $this->normalizeValue($data['latitude']);
            $data['longitude'] = $this->normalizeValue($data['longitude']);

            return $data;
        }

        return [];
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        $data = [];

        if (is_array($value)) {
            $data = [$key => $value];
        } else {
            $data = $value ? [$key => json_decode($value,true)] : [];

            if (empty($data)) {
                $data[$key]['latitude'] = 0;
                $data[$key]['longitude'] = 0;

            }
        }

        return json_encode($data[$key]);
    }

    /**
     * Add trailing 'zero' symbol if coordinate value has one digit before the dot.
     * Without it Longitude or Latitude values in it's input fields displays wrong value.
     *
     * @param string $value
     *
     * @return string
     */
    protected function normalizeValue(string $value): string
    {
        return rtrim(sprintf("%09.6f", $value), '0');
    }
}
