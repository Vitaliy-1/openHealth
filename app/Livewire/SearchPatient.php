<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class SearchPatient extends Component
{
    public string $firstName = '';
    public string $lastName = '';
    public string $birthDate = '';
    public string $secondName = '';
    public string $email = '';
    public string $ipn = '';
    public string $phone = '';
    public string $birthCertificate = '';
    public ?array $patients = null;

    /**
     * Search for patient in eHealth
     */
    public function search(): void
    {
        $response = Http::acceptJson()
            ->connectTimeout(Config::get('ehealth.api.timeout'))
            ->get(
            Config::get('ehealth.api.domain') . '/api/persons',
                [
                    'first_name' => $this->firstName,
                    'last_name' => $this->lastName,
                    'birth_date' => $this->birthDate,
                    'second_name' => $this->secondName,
                    'email' => $this->email,
                    'ipn' => $this->ipn,
                    'phone' => $this->phone,
                    'birth_certificate' => $this->birthCertificate,
                ]
        );

        if ($response->successful()) {
            $results = json_decode($response->body(), true);

            $this->patients = $results['data'] ?? [];
            return;
        }

        // if response wasn't received, sent an error message
        if ($response->failed()) {

        }
    }

    public function render()
    {
        return view('livewire.search-patient');
    }


}
