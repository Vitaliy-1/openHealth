<?php

namespace App\Traits;

use App\Helpers\JsonHelper;

trait FormTrait
{

    public bool|string $showModal = false;

    public array $phones = ['type' => '', 'number' => ''];

    public ?array $dictionaries = [];

    public function openModal($modal = true): void
    {
        $this->showModal = $modal;
        $this->phones = [];
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->phones = [];
    }

    public function addRowPhone(): array
    {
        return $this->phones[] = ['type' => '', 'number' => ''];
    }

    public function removePhone($key): void
    {
        if (isset($this->phones[$key])) {
            unset($this->phones[$key]);
        }
    }

    public function getDictionary(): void
    {
        $this->dictionaries = JsonHelper::searchValue('DICTIONARIES_PATH', $this->dictionaries_field ?? []  );
    }
}
