<?php

declare(strict_types=1);

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Collection;

trait FormTrait
{
    /**
     * @var bool|string
     */
    public bool|string $showModal = false;

    /**
     * @var array|null
     */
    public ?array $dictionaries = [];

    /**
     * Opens a modal.
     *
     * @param  bool|string  $modal  Determines if the modal should be shown.
     * @return void
     */
    public function openModal(bool|string $modal = true): void
    {
        $this->showModal = $modal;
    }

    /**
     * Closes the modal by setting the $showModal property to false.
     *
     * @return void
     */
    public function closeModal(): void
    {
        $this->showModal = false;
    }

    protected function &handleDynamicProperty(string $property): mixed
    {
        $propertyParts = explode('.', $property);
        $currentProperty = &$this;

        foreach ($propertyParts as $part) {
            if (is_object($currentProperty)) {
                if (!property_exists($currentProperty, $part)) {
                    $currentProperty->{$part} = []; // Create a new property as an array
                }

                $currentProperty = &$currentProperty->{$part};
            } // If $currentProperty is an array
            elseif (is_array($currentProperty)) {
                if (!array_key_exists($part, $currentProperty)) {
                    $currentProperty[$part] = []; // Add a new key
                }

                $currentProperty = &$currentProperty[$part];
            }
        }

        return $currentProperty;
    }

    /**
     * Retrieves and sets the dictionaries by searching for the value of 'DICTIONARIES_PATH' in the dictionaries field.
     *
     * @return void
     */
    protected function getDictionary(): void
    {
        $this->dictionaries = dictionary()->getDictionaries($this->dictionaryNames ?? []);
    }

    /**
     * Filter and keep only the specified keys in a dictionaries array.
     *
     * @param  array  $keys  The keys to keep in the dictionaries array
     * @param  string  $dictionaries  The name of the dictionaries array to filter
     * @return array
     */
    protected function getDictionariesFields(array $keys, string $dictionaries): array
    {
        // If the dictionaries array exists and is an array, filter and keep only the specified keys
        if (isset($this->dictionaries[$dictionaries]) && is_array($this->dictionaries[$dictionaries])) {
            // Filter and keep only the specified keys in the dictionaries array
            return array_intersect_key($this->dictionaries[$dictionaries], array_flip($keys));
        }

        // return an empty array if the dictionaries array does not exist or is not an array
        return [];
    }

    /**
     * Closes the modal by setting the showModal property to false.
     */
    public function closeModalModel(): void
    {
        $this->showModal = false;
    }

    /**
     * Convert all keys in address array (course, only of need to) to the snake-case format.
     * This need to do because DB table store it's attributes in the snake-case
     *
     * @param  array  $array
     * @return array
     */
    public function convertArrayKeysToSnakeCase(array $array): array
    {
        return collect($array)
            ->mapWithKeys(function ($value, $key) {
                return is_array($value)
                    ? [Str::snake($key) => $this->convertArrayKeysToSnakeCase($value)]
                    : [Str::snake($key) => $value];
            })
            ->toArray();
    }

    /**
     * Convert all keys in address array (course, only of need to) to the CamelCase format.
     * This need to do because DB table has it's attributes in the snake-case but the form uses camelCase
     *
     * @param  array  $array
     * @return array
     */
    public function convertArrayKeysToCamelCase(array $array): array
    {
        return collect($array)
            ->mapWithKeys(function ($value, $key) {
                return is_array($value)
                    ? [Str::camel($key) => $this->convertArrayKeysToCamelCase($value)]
                    : [Str::camel($key) => $value];
            })
            ->toArray();
    }

    /**
     * Retrieves all attributes from a model object (includes relations).
     *
     * @param  object  $model  The model object to extract attributes from
     *
     * @return array An array containing all attributes of the model
     */
    protected function getAllAttributes(object $model): array
    {
        $arr = $model->getAttributes();
        $relations = $model->getRelations();

        foreach ($relations as $key => $relation) {
            if ($relation instanceof Collection) {
                $relationData = [];

                foreach ($relation as $index => $relationModel) {
                    $relationData[] = [$index => $relationModel->getAttributes()];
                }
            } else {
                $relationData = $relation->getAttributes();
            }

            $arr = array_merge($arr, [$key => $relationData]);
        }

        // return $this->flattenArray($arr);
        return $arr;
    }

    /**
     * Flattens a multi-dimensional array.
     * All non-firstlevel keys are concatenated with a dot.
     *
     * @param  array  $array  The multi-dimensional array to flatten
     *
     * @param  string  $keyPrefix  The prefix to add to the keys
     *
     * @return array The flattened array
     */
    protected function flattenArray(array $array, string $keyPrefix = ''): array
    {
        $flattenedArray = [];

        foreach ($array as $key => $value) {
            $key = $keyPrefix ? $keyPrefix . '.' . $key : $key;

            if (is_array($value)) {
                $flattenedArray = array_merge($flattenedArray, $this->flattenArray($value, $key));
            } else {
                $flattenedArray[$key] = $value;
            }
        }

        return $flattenedArray;
    }
}
