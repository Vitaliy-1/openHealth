<?php

namespace App\Livewire\Patient;

use App\Classes\eHealth\Api\PersonApi;
use App\Classes\eHealth\Exceptions\ApiException;
use App\Livewire\Patient\Forms\Api\PatientRequestApi;
use App\Livewire\Patient\Forms\PatientFormRequest;
use App\Models\Person;
use App\Traits\FormTrait;
use App\Traits\InteractsWithCache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class PatientForm extends Component
{
    use FormTrait, InteractsWithCache;

    private const string CACHE_PREFIX = 'register_patient_form';

    public string $mode = 'create';
    public PatientFormRequest $patientRequest;
    public Person $patient;
    public string $requestId;
    public string $patientId;
    protected string $patientCacheKey;
    public int $keyProperty;
    public bool $noTaxId = false;
    public bool $isIncapable = false;

    protected $listeners = ['addressDataFetched'];
    public array $dictionaries_field = [
        'DOCUMENT_TYPE',
        'DOCUMENT_RELATIONSHIP_TYPE',
        'GENDER',
        'PHONE_TYPE',
        'PREFERRED_WAY_COMMUNICATION',
        'STREET_TYPE',
    ];

    public function boot(): void
    {
        $this->patientCacheKey = self::CACHE_PREFIX . '-' . Auth::user()->id . '-' . Auth::user()->legalEntity->uuid;
    }

    public function mount(Request $request, string $id = ''): void
    {
        if ($request->has('store_id')) {
            $this->requestId = $request->input('store_id');
        }

        if (!empty($id)) {
            $this->patientId = $id;
        }

        $this->getPatient();
        $this->getDictionary();
    }

    public function render()
    {
        return view('livewire.patient.patient-form');
    }

    public function hydrate(): void
    {
        if ($this->patientRequest->documents && $this->patientRequest->documents_relationship) {
            $this->getPatient();
        }
    }

    /**
     * Initialize the creation mode for a specific model.
     *
     * @param string $model The model type to initialize for creation.
     * @return void
     */
    public function create(string $model): void
    {
        $this->mode = 'create';
        $this->patientRequest->{$model} = [];
        $this->openModal($model);
        $this->getPatient();
    }

    /**
     * Store valid data for a specific model.
     *
     * @param string $model The model type to store data for.
     * @return void
     * @throws ValidationException
     */
    public function store(string $model): void
    {
        $this->patientRequest->rulesForModelValidate($model);
        $this->fetchDataFromAddressesComponent();
        $this->resetErrorBag();

        if (isset($this->requestId)) {
            $this->storeCachePatient($model);
        }

        $this->closeModalModel();
        $this->dispatch('flashMessage', [
            'message' => __('Інформацію успішно оновлено'),
            'type' => 'success'
        ]);

        $this->getPatient();
    }

    /**
     * Store patient data in cache for a specific model.
     *
     * @param string $model The model type to store data for.
     * @return void
     */
    protected function storeCachePatient(string $model): void
    {
        $this->storeCacheData($this->patientCacheKey, $model, 'patientRequest', ['patient']);
    }

    public function signedComplete(string $model): void
    {
        $open = $this->patientRequest->validateBeforeSendApi();
        if ($open['error'] ) {
            $this->dispatch('flashMessage', ['message' => $open['message'], 'type' => 'error']);
        }
        else{
            $this->openModal($model);
        }

        $this->sendApiRequest();
    }

    /**
     * @throws ApiException
     */
    protected function sendApiRequest(): void
    {
        $cacheData = $this->getCache($this->patientCacheKey);

        if (isset($this->requestId, $cacheData[$this->requestId])) {
            $this->patientRequest->fill($cacheData[$this->requestId]);
        }

        $requestData = PatientRequestApi::buildCreatePersonRequest($cacheData[$this->requestId], $this->noTaxId, $this->isIncapable);
        PersonApi::createPersonRequest($requestData);

        $this->getPatient();
    }

    /**
     * Get all data about the patient from the cache.
     *
     * @return void
     */
    protected function getPatient(): void
    {
        if (isset($this->requestId) && $this->hasCache($this->patientCacheKey)) {
            $patientData = $this->getCache($this->patientCacheKey);

            if (isset($patientData[$this->requestId])) {
                $this->patient = (new Person())->forceFill($patientData[$this->requestId]);

                if (!empty($this->patient->patient)) {
                    $this->patientRequest->fill(
                        [
                            'patient' => $this->patient->patient,
                            'documents' => $this->patient->documents ?? [],
                            'addresses' => $this->patient->addresses ?? [],
                            'documents_relationship' => $this->patient->documents_relationship ?? [],
                        ]
                    );
                }
            }
        }
    }

    /**
     * Initialize the edit mode for a specific model.
     *
     * @param string $model The model type to initialize for editing.
     * @param int $keyProperty The key property used to identify the specific item to edit (optional).
     * @return void
     */
    public function edit(string $model, int $keyProperty): void
    {
        $this->keyProperty = $keyProperty;
        $this->mode = 'edit';
        $this->openModal($model);

        if (isset($this->requestId)) {
            $this->editCachePatient($model, $keyProperty);
        }
    }

    /**
     * Update the patient request data with cached data for a specific model.
     *
     * @param string $model The model type to update the data for.
     * @param int $keyProperty The key property used to identify the specific item to update (optional).
     * @return void
     */
    protected function editCachePatient(string $model, int $keyProperty): void
    {
        $cacheData = $this->getCache($this->patientCacheKey);

        if (empty($keyProperty) && $keyProperty !== 0) {
            $this->patientRequest->{$model} = $cacheData[$this->requestId][$model];
        } else {
            $this->patientRequest->{$model} = $cacheData[$this->requestId][$model][$keyProperty];
        }
    }

    /**
     * Dispatch an event to fetch address data from the addresses component.
     *
     * @return void
     */
    public function fetchDataFromAddressesComponent(): void
    {
        $this->dispatch('fetchAddressData');
    }

    /**
     * Updates the patient request with fetched address data and stores it in the cache.
     *
     * @param array $addressData An associative array containing address data for the patient.
     * @return void
     */
    public function addressDataFetched(array $addressData): void
    {
        $this->patientRequest->addresses = $addressData;
        $this->putAddressesInCache('addresses', $addressData);
    }

    /**
     * Updates the cache with the provided data under a specific key for the current request ID.
     *
     * @param string $key The key under which the data should be stored in the cache (e.g., 'addresses').
     * @param array $data The data to be stored in the cache.
     * @return void
     */
    private function putAddressesInCache(string $key, array $data): void
    {
        $cacheData = $this->getCache($this->patientCacheKey) ?? [];
        $cacheData[$this->requestId][$key] = $data;

        $this->putCache($this->patientCacheKey, $cacheData);
    }

    /**
     * Update the data for a specific model and key property.
     *
     * @param string $model The model type to update the data for.
     * @param int $keyProperty The key property used to identify the specific item to update.
     * @return void
     * @throws ValidationException
     */
    public function update(string $model, int $keyProperty): void
    {
        $this->patientRequest->rulesForModelValidate($model);
        $this->resetErrorBag();

        if (isset($this->requestId)) {
            $this->updateCachePatient($model, $keyProperty);
        }

        $this->closeModalModel($model);
    }

    /**
     * Update the cached data for a specific model and key property.
     *
     * @param string $model The model type to update the data for.
     * @param int $keyProperty The key property used to identify the specific item to update.
     * @return void
     */
    protected function updateCachePatient(string $model, int $keyProperty): void
    {
        if ($this->hasCache($this->patientCacheKey)) {
            $cacheData = $this->getCache($this->patientCacheKey);
            $cacheData[$this->requestId][$model][$keyProperty] = $this->patientRequest->{$model};

            $this->putCache($this->patientCacheKey, $cacheData);
        }
    }

    /**
     * Close the modal and optionally reset the data for a specific model.
     *
     * @param string|null $model The model type to reset the data for (optional).
     * @return void
     */
    public function closeModalModel(string $model = null): void
    {
        if (!empty($model)) {
            $this->patientRequest->{$model} = [];
        }

        $this->closeModal();
        $this->getPatient();
    }
}
