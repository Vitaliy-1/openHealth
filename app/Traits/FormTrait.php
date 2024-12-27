<?php

namespace App\Traits;

use App\Helpers\JsonHelper;
use App\Models\Employee;
use Illuminate\Support\Arr;


/**
 *
 */
trait FormTrait
{

    /**
     * @var bool|string
     */
    public bool|string $showModal = false;

    /**
     * @var array|array[]
     */
    public array $phones = [
        ['type' => '', 'number' => '']
    ];

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

    /**
     * Add a new phone row to the list of phones.
     *
     * @return array|null The newly added phone row
     */
    public function addRowPhone($property = '', $value = 'phones'): array|null
    {
        if (!empty($property)) {
            return $this->handleDynamicProperty($property)[$value][] = ['type' => '', 'number' => ''];;
        }

        return null;
    }

    /**
     * Removes a phone number from the list of phones.
     *
     * @param  string  $key  The key of the phone number to be removed
     * @param  string  $property  The property of the phone number to be removed
     * @param  string  $value  The value of the phone number to be removed
     * @return void
     */
    public function removePhone(string $key, string $property = '', string $value = 'phones'): void
    {
        if (!empty($property)) {
            // Remove the phone number from the property
           $this->handleDynamicProperty($property);
            // Remove the phone number from the property
            if (isset($this->handleDynamicProperty($property)[$value][$key])) {
                unset($this->handleDynamicProperty($property)[$value][$key]);
            }
        }
    }

    public function &handleDynamicProperty(string $property)
    {
        $propertyParts = explode('.', $property);
        $currentProperty = &$this;
        foreach ($propertyParts as $part) {
            if (property_exists($currentProperty, $part)) {
                $currentProperty = &$currentProperty->{$part};
            } else {
                if (is_object($currentProperty)) {
                    $currentProperty->{$part} = [];
                } else {
                    $currentProperty[$part] = [];
                }
                $currentProperty = &$currentProperty->{$part};
            }
        }
        return $currentProperty;
    }


    /**
     * Retrieves and sets the dictionaries by searching for the value of 'DICTIONARIES_PATH' in the dictionaries field.
     *
     * @return void
     */
//    public function getDictionary(): void
//    {
//        $this->dictionaries = JsonHelper::searchValue('DICTIONARIES_PATH', $this->dictionaries_field ?? []);
//    }

    public function getDictionary() {

        $this->dictionaries = dictionary()->getDictionaries( $this->dictionaries_field ?? [], true);
    }

    /**
     * Filter and keep only the specified keys in a dictionaries array.
     *
     * @param  array  $keys  The keys to keep in the dictionaries array
     * @param  string  $dictionaries  The name of the dictionaries array to filter
     * @return void
     */
    public function getDictionariesFields(array $keys, string $dictionaries): array
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
}

