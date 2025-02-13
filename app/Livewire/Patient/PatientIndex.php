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

        $this->patients = array_merge(
            $this->setPersonStatus($patientsFromEHealth, 'ЕСОЗ'),
            $this->setPersonStatus($personRequests, 'ЗАЯВКА'),
            $this->setPersonStatus($persons, 'ВНУТРІШНІЙ')
        );

        $this->searchPerformed = true;
    }

    public function render(): View
    {
        $paginatedPatients = $this->createPaginator($this->patients);

        return view('livewire.patient.index', [
            'paginatedPatients' => $paginatedPatients
        ]);
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
