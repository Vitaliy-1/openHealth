<?php

declare(strict_types=1);

namespace App\Livewire\Patient;

use App\Classes\eHealth\Api\PersonApi;
use App\Classes\eHealth\Exceptions\ApiException;
use App\Livewire\Patient\Forms\Api\PatientRequestApi;
use App\Livewire\Patient\Forms\PatientFormRequest;
use App\Models\Person\Person;
use App\Models\Person\PersonRequest;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class PatientIndex extends Component
{
    /**
     * List of founded person.
     * @var array
     */
    public array $patients = [];

    /**
     * Patient data from eHealth response.
     * @var array
     */
    public array $originalPatients = [];

    public PatientFormRequest $form;

    /**
     * Check if the search person's request found someone.
     *
     * @var bool
     */
    public bool $searchPerformed = false;

    public function render(): View
    {
        $paginatedPatients = $this->createPaginator($this->patients);

        return view('livewire.patient.index', [
            'paginatedPatients' => $paginatedPatients
        ]);
    }

    /**
     * Search for person with provided filters.
     *
     * @return void
     * @throws ApiException|ValidationException
     */
    public function searchForPerson(): void
    {
        $this->form->rulesForModelValidate('patientsFilter');

        // Search in our DB
        $this->originalPatients = Person::where(arrayKeysToSnake($this->form->patientsFilter))
            ->with('phones')
            ->select([
                'id', 'uuid', 'first_name', 'last_name', 'second_name', 'birth_date', 'tax_id', 'verification_status'
            ])
            ->get()
            ->toArray();

        // Don't use phone when searching locally.
        unset($this->form->patientsFilter['phoneNumber']);
        // Search for application
        $personRequests = PersonRequest::where(arrayKeysToSnake($this->form->patientsFilter))
            ->where('status', 'APPLICATION')
            ->with('phones')
            ->select(['id', 'status', 'first_name', 'last_name', 'second_name', 'birth_date', 'tax_id'])
            ->get()
            ->toArray();

        // If found in our DB, show that result
        if (!empty($this->originalPatients)) {
            $this->patients = array_merge(
                $this->setPersonStatus($personRequests, 'APPLICATION'),
                $this->originalPatients = array_map(static function ($patient) {
                    return array_merge($patient, ['status' => $patient['verification_status']]);
                }, $this->originalPatients)
            );
        } else {
            // Otherwise search in eHealth
            $buildSearchRequest = PatientRequestApi::buildSearchForPerson($this->form->patientsFilter);
            $this->originalPatients = PersonApi::searchForPersonByParams($buildSearchRequest);

            $this->patients = array_merge(
                $this->setPersonStatus($personRequests, 'APPLICATION'),
                $this->originalPatients = $this->setPersonStatus($this->originalPatients, 'eHEALTH'),
            );
        }

        $this->searchPerformed = true;
        $this->dispatch('patientsUpdated', $this->patients);
    }

    /**
     * Redirect to patient data route.
     *
     * @param  array  $patientData  The associative array containing patient details.
     * @return void
     */
    public function redirectToPatient(array $patientData): void
    {
        $this->handleRedirect($patientData, 'patient.patient-data');
    }

    /**
     * Redirect to create encounter route.
     *
     * @param  array  $patientData  The associative array containing patient details.
     * @return void
     */
    public function redirectToEncounter(array $patientData): void
    {
        $this->handleRedirect($patientData, 'encounter.form');
    }

    /**
     * Delete person request.
     *
     * @param  int  $id
     * @return void
     */
    public function removeApplication(int $id): void
    {
        PersonRequest::destroy($id);
        $this->dispatch('patientRemoved', $id);
    }

    /**
     * Stores patient data in the DB and redirects to route by name.
     *
     * @param  array  $patientData  The associative array containing patient details.
     * @param  string  $routeName
     * @return void
     */
    private function handleRedirect(array $patientData, string $routeName): void
    {
        $originalPatientData = collect($this->getOriginalPatients())
            ->first(function ($patient) use ($patientData) {
                return (isset($patientData['id']) && $patient['id'] === $patientData['id']) ||
                    (isset($patientData['uuid']) && $patient['uuid'] === $patientData['uuid']);
            });

        // Check if the array has not changed and if the UUID is valid.
        if (($patientData !== $originalPatientData) && uuid_is_valid($originalPatientData['uuid'] ?? $originalPatientData['id'])) {
            $this->dispatch('flashMessage', [
                'message' => 'Виникла помилка, зверніться до адміністратора.',
                'type' => 'error'
            ]);
            return;
        }

        $person = Person::firstWhere('uuid', $originalPatientData['uuid'] ?? $originalPatientData['id']);

        // Crete person in DB if not exist.
        if (!$person) {
            $person = $this->storeNewPerson($originalPatientData);
        }

        $this->redirectRoute($routeName, ['id' => $person->id]);
    }

    /**
     * Get the original patient's data.
     *
     * @return array
     */
    private function getOriginalPatients(): array
    {
        return $this->originalPatients;
    }

    /**
     * Store new person from eHealth in DB.
     *
     * @param  array  $originalPatientData
     * @return Person|null
     */
    private function storeNewPerson(array $originalPatientData): ?Person
    {
        try {
            $person = Person::firstOrCreate(
                ['uuid' => $originalPatientData['uuid'] ?? $originalPatientData['id']],
                $originalPatientData
            );

            if (isset($patientData['phones'])) {
                $person->phones()->createMany($originalPatientData['phones']);
            }

            return $person;
        } catch (Exception $e) {
            $this->dispatch('flashMessage', [
                'message' => 'Виникла помилка, зверніться до адміністратора.',
                'type' => 'error'
            ]);

            Log::channel('db_errors')->error('Error while creating new person', [
                'error' => $e->getMessage()
            ]);

            return null;
        }
    }

    /**
     * Creates a pagination instance for the given array of items.
     *
     * @param  array  $items  The array of items to paginate
     * @param  int  $perPage  Number of items per page
     * @return LengthAwarePaginator
     */
    private function createPaginator(array $items, int $perPage = 5): LengthAwarePaginator
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage() ?? 1;
        $collection = collect($items);

        return new LengthAwarePaginator(
            $collection->forPage($currentPage, $perPage),
            $collection->count(),
            $perPage,
            $currentPage
        );
    }

    /**
     * Add status to patients.
     *
     * @param  array  $persons
     * @param  string  $status
     * @return array
     */
    private function setPersonStatus(array $persons, string $status): array
    {
        return array_map(static function ($patient) use ($status) {
            $patient['status'] = $status;
            return $patient;
        }, $persons);
    }
}
