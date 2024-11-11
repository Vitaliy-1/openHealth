<?php

namespace App\Traits;

use App\Helpers\JsonHelper;
use App\Models\Employee;

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
     * @param bool|string $modal Determines if the modal should be shown.
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
     * @return array The newly added phone row
     */
    public function addRowPhone(): array
    {
        return $this->phones[] = ['type' => '', 'number' => ''];
    }

    /**
     * Removes a phone number from the list of phones.
     *
     * @param string $key The key of the phone number to be removed
     * @return void
     */
    public function removePhone(string $key): void
    {
        if (isset($this->phones[$key])) {
            unset($this->phones[$key]);
        }
    }

    /**
     * Retrieves and sets the dictionaries by searching for the value of 'DICTIONARIES_PATH' in the dictionaries field.
     *
     * @return void
     */
    public function getDictionary(): void
    {
        $this->dictionaries = JsonHelper::searchValue('DICTIONARIES_PATH', $this->dictionaries_field ?? []);
    }

    /**
     * Filter and keep only the specified keys in a dictionaries array.
     *
     * @param array $keys The keys to keep in the dictionaries array
     * @param string $dictionaries The name of the dictionaries array to filter
     * @return void
     */
    public function getDictionariesFields(array $keys, string $dictionaries): void{
        // Check if the dictionaries array exists and is an array
        if (is_array($this->dictionaries[$dictionaries])) {
            // Filter the dictionaries array to keep only the specified keys
            $this->dictionaries[$dictionaries] = array_filter(
                $this->dictionaries[$dictionaries],
                function ($key) use ($keys) {
                    return in_array($key, $keys);
                },
                ARRAY_FILTER_USE_KEY
            );
        }
    }

    /**
     * Closes the modal by setting the showModal property to false.
     */
    public function closeModalModel(): void
    {
        $this->showModal = false;
    }
}

