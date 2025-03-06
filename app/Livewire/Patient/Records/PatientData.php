<?php

declare(strict_types=1);

namespace App\Livewire\Patient\Records;

use App\Classes\eHealth\Api\PersonApi;
use App\Classes\eHealth\Exceptions\ApiException;
use App\Livewire\Patient\Forms\Api\PatientRequestApi;
use App\Models\Person\Person;
use App\Repositories\PersonRepository;
use Illuminate\Contracts\View\View;

class PatientData extends BasePatientComponent
{
    public array $phones = [];
    public array $confidantPersonRelationships;
    public array $authenticationMethods;

    protected function initializeComponent(): void
    {
        $patient = Person::with('phones')
            ->where('id', $this->id)
            ->first()
            ?->toArray();

        $this->phones = $patient['phones'] ?? [];
    }

    public function render(): View
    {
        return view('livewire.patient.records.patient-data');
    }

    /**
     * Get patient verification status.
     *
     * @return void
     */
    public function getVerificationStatus(): void
    {
        try {
            $personVerificationDetails = PersonApi::getPersonVerificationDetails($this->uuid);
            PersonRepository::updateVerificationStatusById(
                $this->uuid,
                $personVerificationDetails['verification_status']
            );

            $this->verificationStatus = $personVerificationDetails['verification_status'];
        } catch (ApiException) {
            $this->dispatch('flashMessage', [
                'message' => __('Не вдалося отримати верифікаційний статус. Спробуйте пізніше.'),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Get patient confidant persons.
     *
     * @return void
     */
    public function getConfidantPersons(): void
    {
        try {
            $buildConfidantRelationshipRequest = PatientRequestApi::buildGetConfidantPersonRelationships(false);
            $confidantPersonRelationships = PersonApi::getConfidantPersonRelationships(
                $this->uuid,
                $buildConfidantRelationshipRequest
            );

            $this->confidantPersonRelationships = $confidantPersonRelationships;
        } catch (ApiException) {
            $this->dispatch('flashMessage', [
                'message' => __('Не вдалося отримати законного представника. Спробуйте пізніше.'),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Get patient authentication methods.
     *
     * @return void
     */
    public function getAuthenticationMethods(): void
    {
        try {
            $authenticationMethods = PersonApi::getAuthenticationMethods($this->uuid);

            $this->authenticationMethods = $authenticationMethods;
        } catch (ApiException) {
            $this->dispatch('flashMessage', [
                'message' => __('Не вдалося отримати методи автентифікації. Спробуйте пізніше.'),
                'type' => 'error'
            ]);
        }
    }
}
