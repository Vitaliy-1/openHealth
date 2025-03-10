<?php

declare(strict_types=1);

namespace App\Services\Dictionary;

use Illuminate\Support\Collection;

class Dictionary extends Collection
{
    /**
     * Set key value pair.
     *
     * @param  string  $key
     * @param  array  $value
     * @return $this
     */
    public function setValue(string $key, array $value): self
    {
        $this->items[$key] = collect($value);
        return $this;
    }

    /**
     * Return values that allowed by provided keys.
     *
     * @param  array  $keys  Allowed keys.
     * @return self
     */
    public function allowedKeys(array $keys): self
    {
        return $this->only($keys);
    }

    /**
     * Convert to array.
     *
     * @return array
     */
    public function toArrayRecursive(): array
    {
        return $this->map(function ($item) {
            return $item instanceof Collection ? $item->toArray() : $item;
        })->toArray();
    }

    /**
     * Get value by provided key.
     *
     * @param  string  $key
     * @return mixed
     */
    public function getValue(string $key): mixed
    {
        return $this->items[$key] ?? null;
    }

    /**
     * Check if the Dictionary, and it's subDictionaries has the specified key
     * Recursive.
     *
     * @param  string  $key
     *
     * return bool
     */
    public function hasKey(string $key): bool
    {
        return in_array($key, $this->getAllKeys(), true);
    }

    /**
     * Get 'first level' keys from dictionary. The keys from subDictionaries are not included.
     * Not recursive!
     *
     * @return array
     */
    public function getKeys(): array
    {
        $keys = [];

        foreach ($this->items as $key => $value) {
            $keys[] = $key;
        }

        return $keys;
    }

    /**
     * Get all keys from dictionary.
     * The keys from subDictionaries and numeric keys are included too.
     * Recursive.
     *
     * @return array
     */
    public function getAllKeys(): array
    {
        $keys = [];

        foreach ($this->items as $key => $value) {
            $keys[] = $key;

            if ($value instanceof self) {
                $keys = array_merge($keys, $value->getKeys());
            }
        }

        return $keys;
    }
}
