<?php

declare(strict_types=1);

namespace App\Livewire\Patient;

use App\Classes\eHealth\Api\PersonApi;
use App\Classes\eHealth\Exceptions\ApiException;
use App\Livewire\Patient\Forms\Api\PatientRequestApi;
use App\Livewire\Patient\Forms\PatientFormRequest;
use App\Models\Person\Person;
use App\Models\Person\PersonRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class PatientIndex extends Component
{
    /**
     * List of founded person.
     * @var array
     */
    public array $patients = [];
    public PatientFormRequest $patientRequest;

    /**
     * List of table headers.
     * @var array
     */
    public array $tableHeaders = [];

    /**
     * Check if the search person's request found someone.
     *
     * @var bool
     */
    public bool $searchPerformed = false;

    /**
     * Toggle displaying additional parameters.
     * @var bool
     */
    public bool $showAdditionalParams = false;

    public function mount(): void
    {
        $this->setTableHeaders();
    }

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
     * @param  string  $model
     * @return void
     * @throws ApiException|ValidationException
     */
    public function searchForPerson(string $model): void
    {
        $this->patientRequest->rulesForModelValidate($model);

        // Search in eHealth
        $buildSearchRequest = PatientRequestApi::buildSearchForPerson($this->patientRequest->patientsFilter);
        $patientsFromEHealth = PersonApi::searchForPersonByParams($buildSearchRequest);

        // Don't use phone when searching locally.
        unset($this->patientRequest->patientsFilter['phoneNumber']);
        // Search for application
        $personRequests = PersonRequest::where(arrayKeysToSnake($this->patientRequest->patientsFilter))
            ->where('status', 'APPLICATION')
            ->with('phones')
            ->select(['id', 'status', 'first_name', 'last_name', 'second_name', 'birth_date', 'tax_id'])
            ->get()
            ->toArray();

        if (empty($patientsFromEHealth)) {
            $persons = Person::where(arrayKeysToSnake($this->patientRequest->patientsFilter))
                ->with('phones')
                ->get()
                ->toArray();
        } else {
            $persons = [];
        }

        $personsWithStatuses = array_map(function ($person) {
            return $this->setPersonStatus([$person], $person['verification_status']);
        }, $persons);

        $this->patients = array_merge(
            $this->setPersonStatus($patientsFromEHealth, 'eHEALTH'),
            $this->setPersonStatus($personRequests, 'APPLICATION'),
            ...$personsWithStatuses
        );

        $this->searchPerformed = true;
    }

    /**
     * Stores patient data in the session and redirects to the patient's data tab.
     *
     * @param  array  $patientData  The associative array containing patient details.
     * @return void
     */
    public function redirectToPatient(array $patientData): void
    {
        session(["temp_patient_data_" . $patientData['id'] => $patientData]);

        $this->redirectRoute('patient.tabs', ['id' => $patientData['id'], 'tab' => 'patient-data']);
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
     * Set headers for the persons table.
     *
     * @return void
     */
    private function setTableHeaders(): void
    {
        $this->tableHeaders = [
            __('ПІБ'),
            __('forms.phones'),
            __('Д.Н.'),
            __('forms.RNOCPP') . '(' . __('forms.ipn') . ')',
            __('forms.birthCertificate'),
            __('forms.status'),
            __('forms.action')
        ];
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
