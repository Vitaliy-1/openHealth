<?php

namespace App\Livewire\Patient;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class PatientIndex extends Component
{
    /**
     * @var object|null
     */
    public ?object $patients = null;

    /**
     * @var object|null
     */
    public ?object $patient_show = null;
    public string $firstName = '';
    public string $lastName = '';
    public string $birthDate = '';
    public string $secondName = '';
    public string $email = '';
    public string $ipn = '';
    public string $phone = '';
    public string $birthCertificate = '';

    /**
     * Search for patient in eHealth
     */

    public function search(): void
    {

    }

    public function render()
    {
        return view('livewire.patient.index');
    }
}
