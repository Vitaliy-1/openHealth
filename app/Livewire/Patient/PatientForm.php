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
    public ?array $dictionaries_field = [
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
        $this->dispatch('flashMessage', ['message' => __('Інформацію успішно оновлено'), 'type' => 'success']);

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

    public function signedComplete(): void
    {
        $this->sendApiRequest();
    }

    /**
     * @throws ApiException
     */
    protected function sendApiRequest()
    {
        $cacheData = $this->getCache($this->patientCacheKey);

        if (isset($this->requestId, $cacheData[$this->requestId])) {
            $this->patientRequest->fill($cacheData[$this->requestId]);
        }

//        $getPatientRequestListData = $this->buildGetPatientRequestList('NEW', 1, 50);
//        $personRequestList = PersonApi::getCreatedPersonsList($getPatientRequestListData);
        // f3a09bc9-33c5-4719-b51b-c334c8699cd0 (із СМС)

        $personById = PersonApi::getCreatedPersonById('f3a09bc9-33c5-4719-b51b-c334c8699cd0');
        dd($personById);
//        $resendSms = PersonApi::resendAuthorizationSms('f3a09bc9-33c5-4719-b51b-c334c8699cd0');

//        $data = $this->buildPatientRequest();
//        $patient = PersonApi::createPersonRequest($data);

//        $approvePatientRequestData = $this->buildApprovePatientRequest(9387);
//        $patientApprove = PersonApi::approvePersonRequest($patient['data']['id'], $approvePatientRequestData);
//        $patientApprove = PersonApi::approvePersonRequest('f3a09bc9-33c5-4719-b51b-c334c8699cd0', $approvePatientRequestData);

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

    protected function buildPatientRequest(): array
    {
        $cacheData = $this->getCache($this->patientCacheKey);

        $patient = $cacheData['patient'];
        $documents = $patient['documents'][0];
        $addresses = $patient['addresses'];
        $phones = $patient['phones'];
        $authentication_methods = $patient['authentication_methods'];
        $emergency_contact = $patient['emergency_contact'];
        $documents_relationship = $patient['documents_relationship'][0] ?? null;

        $patientData = [
//            'id' => '13001c60-45a0-4b5a-b425-9505e1de18bd',
            'first_name' => $patient['first_name'],
            'last_name' => $patient['last_name'],
            'second_name' => $patient['second_name'] ?? '',
            'birth_date' => $patient['birth_date'],
            'birth_country' => $patient['birth_country'],
            'birth_settlement' => $patient['birth_settlement'],
            'gender' => $patient['gender'],
            'email' => $patient['email'] ?? '',
            'no_tax_id' => $this->noTaxId,
            'secret' => $patient['secret'],

            'documents' => [
                [
                    'type' => $documents['type'],
                    'number' => $documents['number'],
                    'issued_by' => $documents['issued_by'],
                    'issued_at' => $documents['issued_at'],
                    'expiration_date' => $documents['expiration_date'] ?? '',
                ]
            ],

            'addresses' => [
                [
                    'type' => $addresses['type'],
                    'country' => $addresses['country'],
                    'area' => $addresses['area'],
                    'region' => $addresses['region'] ?? '',
                    'settlement' => $addresses['settlement'],
                    'settlement_type' => $addresses['settlement_type'],
                    'settlement_id' => $addresses['settlement_id'],
                    'street_type' => $addresses['street_type'] ?? '',
                    'street' => $addresses['street'] ?? '',
                    'building' => $addresses['building'] ?? '',
                    'apartment' => $addresses['apartment'] ?? '',
                    'zip' => $addresses['zip'] ?? '',
                ]
            ],

            'phones' => [
                [
                    'type' => $phones['type'],
                    'number' => $phones['number'],
                ]
            ],

            'authentication_methods' => [
                [
                    'type' => $authentication_methods['type'],
                    // required for type = OTP
                    'phone_number' => $authentication_methods['phone_number'] ?? '',
                    // required for type = THIRD_PERSON
                    'value' => $authentication_methods['value'] ?? '',
                    // required it type = THIRD_PERSON, and optional for type = OTP or OFFLINE
                    'alias' => $authentication_methods['alias'] ?? '',
                ]
            ],

            'unzr' => $documents['unzr'] ?? '',

            'emergency_contact' => (object)
            [
                'first_name' => $emergency_contact['first_name'],
                'last_name' => $emergency_contact['last_name'],
                'second_name' => $emergency_contact['second_name'] ?? '',

                'phones' => [
                    [
                        'type' => $emergency_contact['phones']['type'],
                        'number' => $emergency_contact['phones']['number'],
                    ]
                ],
            ],
        ];

        if (!$this->noTaxId) {
            $patientData['tax_id'] = $patient['tax_id'];
        }

        if ($this->isIncapable) {
            $patientData['confidant_person'] = (object)
            [
                'person_id' => '',

                'documents_relationship' => [
                    [
                        'type' => $documents_relationship['type'],
                        'number' => $documents_relationship['number'],
                        'issued_by' => $documents_relationship['issued_by'] ?? '',
                        'issued_at' => $documents_relationship['issued_at'] ?? '',
                        // ?? schema does not allow additional properties, але в general MIS API є....
//                        'active_to' => $documents_relationship['expiration_date'] ?? '',
                    ]
                ],
            ];
        }

        removeEmptyKeys($patientData);

        $arr = [
            'person' => (object)$patientData,
            'patient_signed' => false,
            'process_disclosure_data_consent' => true,
        ];

        return $arr;
    }

    protected function buildApprovePatientRequest(int $verificationCode): array
    {
        return ['verification_code' => $verificationCode];
    }

    protected function buildGetPatientRequestList(string $status, int $page, int $pageSize): array
    {
        return [
            'status' => $status,
            'page' => $page,
            'page_size' => $pageSize,
        ];
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
