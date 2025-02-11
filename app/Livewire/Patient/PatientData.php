<?php

declare(strict_types=1);

namespace App\Livewire\Patient;

use App\Classes\eHealth\Api\PersonApi;
use App\Classes\eHealth\Exceptions\ApiException;
use App\Enums\Person\AuthenticationMethod;
use App\Livewire\Patient\Forms\Api\PatientRequestApi;
use App\Models\Person\Person;
use App\Repositories\PersonRepository;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class PatientData extends Component
{
    /**
     * Info about the patient.
     * @var array
     */
    public array $patient;

    protected string|int $patientUuid;

    /**
     * Boot the component with required dependencies.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->patientUuid = $this->getPatientUuid();
    }

    public function mount(array $patient): void
    {
        $this->patient = $patient;
    }

    public function render(): View
    {
        return view('livewire.patient.patient-data');
    }

    /**
     * Get patient verification status.
     *
     * @return void
     */
    public function getVerificationStatus(): void
    {
        try {
            $personVerificationDetails = PersonApi::getPersonVerificationDetails($this->patientUuid);
            PersonRepository::updateVerificationStatusById(
                $this->patientUuid,
                $personVerificationDetails['verification_status']
            );

            $this->patient['status'] = $personVerificationDetails['verification_status'];
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
                $this->patientUuid,
                $buildConfidantRelationshipRequest
            );

            $this->patient['confidantPersonRelationships'] = $confidantPersonRelationships;
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
            $authenticationMethods = PersonApi::getAuthenticationMethods($this->patientUuid);
            $authenticationMethod = AuthenticationMethod::tryFrom($authenticationMethods[0]['type'])?->label();

            $this->patient['authenticationMethod'] = $authenticationMethod;
        } catch (ApiException) {
            $this->dispatch('flashMessage', [
                'message' => __('Не вдалося отримати методи автентифікації. Спробуйте пізніше.'),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Get the patient UUID from the URL or find it by ID in the DB.
     *
     * @return string
     */
    protected function getPatientUuid(): string
    {
        return uuid_is_valid($this->patient['id'])
            ? $this->patient['id']
            : Person::find($this->patient['id'])->uuid;
    }
}
