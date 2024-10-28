<?php

namespace App\Traits;

use App\Helpers\JsonHelper;
use App\Models\Employee;

trait FormTrait
{

    public bool|string $showModal = false;

    public array $phones = [
        ['type' => '', 'number' => '']
    ];

    public ?array $dictionaries = [];

    /**
     * @param bool $modal
     * @return void
     */
    public function openModal(bool|string $modal = true): void
    {
        $this->showModal = $modal;
    }

    /**
     * @return void
     */
    public function closeModal(): void
    {
        $this->showModal = false;
    }

    /**
     * @return array[]
     */
    public function addRowPhone(): array
    {
        return $this->phones[] = ['type' => '', 'number' => ''];
    }

    /**
     * @param  string $key
     * @return void
     */
    public function removePhone(string $key): void
    {
        if (isset($this->phones[$key])) {
            unset($this->phones[$key]);
        }
    }

    /**
     * @return void
     */
    public function getDictionary(): void
    {
        $this->dictionaries = JsonHelper::searchValue('DICTIONARIES_PATH', $this->dictionaries_field ?? []  );
    }


    /**
     * @param array $keys
     * @param string $dictionaries
     * @return void
     */
    public function getDictionariesFields(array $keys, string $dictionaries): void{
        if (is_array($this->dictionaries[$dictionaries])) {
            $this->dictionaries[$dictionaries] = array_filter(
                $this->dictionaries[$dictionaries],
                function ($key) use ($keys) {
                    return in_array($key, $keys);
                },
                ARRAY_FILTER_USE_KEY
            );
        }
    }

    public function closeModalModel(): void
    {
        $this->showModal = false;
    }
}

