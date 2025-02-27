<?php

declare(strict_types=1);

namespace App\Livewire\Patient\Records;

use App\Models\Person\Person;
use Livewire\Attributes\Locked;
use Livewire\Component;

abstract class BasePatientComponent extends Component
{
    #[Locked]
    public string $id;
    protected string $uuid;

    public string $firstName;
    public string $lastName;
    public ?string $secondName = null;
    public string $verificationStatus;

    public function boot(): void
    {
        if ($this->id) {
            $this->loadPatientData();
        }
    }

    public function mount(string $id): void
    {
        $this->id = $id;
        $this->initializeComponent();
    }

    /**
     * Get all needed data from DB about patient.
     *
     * @return void
     */
    protected function loadPatientData(): void
    {
        $patient = Person::select(['uuid', 'first_name', 'last_name', 'second_name', 'verification_status'])
            ->where('id', $this->id)
            ->first()
            ?->toArray();

        $this->firstName = $patient['first_name'];
        $this->lastName = $patient['last_name'];
        $this->secondName = $patient['second_name'] ?? null;
        $this->verificationStatus = $patient['verification_status'];
        $this->uuid = $patient['uuid'];
    }

    /**
     * A method that can be overridden in child classes for additional initialization.
     *
     * @return void
     */
    protected function initializeComponent(): void
    {
    }
}
