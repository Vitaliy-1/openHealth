<?php

namespace App\Livewire\Patient;

use App\Classes\Cipher\Traits\Cipher;
use App\Classes\eHealth\Api\PersonApi;
use App\Classes\eHealth\Exceptions\ApiException;
use App\Livewire\Patient\Forms\Api\PatientRequestApi;
use App\Livewire\Patient\Forms\PatientFormRequest;
use App\Models\Person;
use App\Repositories\PersonRepository;
use App\Repositories\PersonRequestRepository;
use App\Traits\FormTrait;
use App\Traits\InteractsWithCache;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Throwable;

class PatientForm extends Component
{
    use FormTrait, InteractsWithCache, WithFileUploads, Cipher;

    private const string CACHE_PREFIX = 'register_patient_form';

    public string $mode = 'create';
    public PatientFormRequest $patientRequest;
    public Person $patient;
    protected PersonRequestRepository $personRequestRepository;
    protected PersonRepository $personRepository;
    public array $documents = [];
    public array $documentsRelationship = [];
    public array $uploadedDocuments = [];
    public string $requestId;
    public string $patientId;
    protected string $patientCacheKey;
    public int $keyProperty;

    public string $viewState = 'default';

    /**
     * Check is store patient was successful
     * @var bool
     */
    public bool $isPatientStored = false;

    /**
     * Mark 'information from the leaflet was communicated to the patient'
     * @var bool
     */
    public bool $isInformed = false;

    /**
     * MPI id of the person
     * @var string|null
     */
    public ?string $id = null;

    /**
     * Check is patient refused to provide РНОКПП/ІПН
     * @var bool
     */
    public bool $noTaxId = false;

    /**
     * Is patient incapable or child less than 14 y.o
     * @var bool
     */
    public bool $isIncapable = false;

    /**
     * Check is person approved
     * @var bool
     */
    public bool $isApproved = false;

    /**
     * KEP key
     * @var object|null
     */
    public ?object $file = null;

    /**
     * Time to resend SMS
     * @var int
     */
    public int $resendCooldown = 60;

    protected $listeners = ['addressDataFetched'];
    public array $dictionaries_field = [
        'DOCUMENT_TYPE',
        'DOCUMENT_RELATIONSHIP_TYPE',
        'GENDER',
        'PHONE_TYPE',
        'STREET_TYPE'
    ];

    public function boot(PersonRequestRepository $personRequestRepository, PersonRepository $personRepository): void
    {
        $this->personRequestRepository = $personRequestRepository;
        $this->personRepository = $personRepository;
        $this->patientCacheKey = self::CACHE_PREFIX . '-' . Auth::user()->legalEntity->uuid;
    }

    /**
     * @throws \App\Classes\Cipher\Exceptions\ApiException
     */
    public function mount(Request $request, string $id = ''): void
    {
        if ($request->has('store_id')) {
            $this->requestId = $request->input('store_id');
        }

        if (!empty($id)) {
            $this->patientId = $id;
        }

        $this->getPatient();
        $this->setCertificateAuthority();
        $this->getDictionary();
    }

    public function render(): View
    {
        return view('livewire.patient.patient-form');
    }

    #[On('confidant-person-selected')]
    public function confidantPersonSelected(string $id): void
    {
        $cacheData = $this->getCache($this->patientCacheKey) ?? [];

        $cacheData[$this->requestId]['documentsRelationship']['personId'] = $id;
        $cacheData[$this->requestId]['patient']['authenticationMethods'][0]['value'] = $id;

        $this->patientRequest->documentsRelationship['personId'] = $id;
        $this->patientRequest->patient['authenticationMethods'][0]['value'] = $id;

        $this->putCache($this->patientCacheKey, $cacheData);
    }

    /**
     * Initialize the creation mode for a specific model.
     *
     * @param  string  $model  The model type to initialize for creation.
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
     * @param  string  $model  The model type to store data for.
     * @return void
     * @throws ValidationException
     */
    public function store(string $model): void
    {
        try {
            $this->patientRequest->rulesForModelValidate($model);
            $this->fetchDataFromAddressesComponent();
            $this->resetErrorBag();

            if (isset($this->requestId)) {
                if (isset($this->patientRequest->documentsRelationship['personId'])) {
                    unset($this->patientRequest->documentsRelationship['personId']);
                }
                $this->storeCachePatient($model);
            }

            $this->closeModalModel();
            $this->dispatch('flashMessage', [
                'message' => __('Інформацію успішно оновлено'),
                'type' => 'success'
            ]);

            $this->getPatient();
            // allow to create person
            if ($model === 'patient') {
                $this->isPatientStored = true;
            }
        } catch (ValidationException $e) {
            $this->dispatch('flashMessage', [
                'message' => __('Помилка валідації'),
                'type' => 'error'
            ]);

            if (isset($this->requestId) && $model === 'patient') {
                $this->storeCachePatient($model);
            }

            throw $e;
        }
    }

    /**
     * Store patient data in cache for a specific model.
     *
     * @param  string  $model  The model type to store data for.
     * @return void
     */
    protected function storeCachePatient(string $model): void
    {
        $this->storeCacheData($this->patientCacheKey, $model, 'patientRequest', ['patient']);
    }

    /**
     * Send API request 'Create Person v2' and show the next page if data is validated.
     *
     * @return void
     * @throws ApiException|Throwable
     */
    public function createPerson(): void
    {
        $open = $this->patientRequest->validateBeforeSendApi();

        if ($open['error']) {
            $this->dispatch('flashMessage', [
                'message' => $open['messages'][0],
                'type' => 'error'
            ]);

            return;
        }

        $response = $this->sendPersonRequest($this->patientRequest->toArray());

        if ($response['meta']['code'] !== 201) {
            $this->dispatch('flashMessage', [
                'message' => 'Виникла помилка, зверніться до адміністратора.',
                'type' => 'error'
            ]);
        }

        if ($response['meta']['code'] === 201) {
            // save in DB
            $personSaved = $this->personRequestRepository->savePersonResponseData($response['data']);

            if (!$personSaved) {
                $this->dispatch('flashMessage', [
                    'message' => 'Виникла помилка, зверніться до адміністратора.',
                    'type' => 'error',
                ]);

                return;
            }

            // Save URLs for uploading in cache
            $cacheData = $this->getCache($this->patientCacheKey);
            $cacheData[$this->requestId]['uploadedDocuments'] = $response['urgent']['documents'];
            $cacheData[$this->requestId]['patient']['id'] = $response['data']['id'];
            $this->putCache($this->patientCacheKey, $cacheData);

            $this->id = $response['data']['id'];
            $this->viewState = 'new';
        }
    }

    /**
     * Upload patient files to the appropriate URL.
     *
     * @param  string  $model
     * @param  string  $documentType
     * @return void
     * @throws ApiException|ValidationException
     */
    public function uploadFile(string $model, string $documentType): void
    {
        $this->patientRequest->rulesForModelValidate($model);

        $document = collect($this->patientRequest->uploadedDocuments)
            ->firstWhere('type', $documentType);

        if ($document) {
            $requestData = PatientRequestApi::buildUploadFileRequest($document['documentsRelationship']);
            $uploadResponse = PersonApi::uploadFileRequest($document['url'], $requestData);

            if (isset($uploadResponse['status']) && $uploadResponse['status'] === 200) {
                $this->dispatch('flashMessage', [
                    'message' => 'Фото успішно завантажено',
                    'type' => 'success',
                ]);
            } else {
                $this->dispatch('flashMessage', [
                    'message' => 'Сталася помилка під час завантаження файлу',
                    'type' => 'error',
                ]);
            }
        }
    }

    /**
     * Build and send API request 'Approve Person v2' and show the next page if data is validated.
     *
     * @param  string  $model
     * @return void
     * @throws ValidationException|ApiException|Throwable
     */
    public function approvePerson(string $model): void
    {
        $this->patientRequest->rulesForModelValidate($model);

        $preRequest = [
            'verification_code' => (int) $this->patientRequest->verificationCode
        ];
        $requestData = schemaService()->requestSchemaNormalize($preRequest, app(PersonApi::class),
            'approveSchemaRequest');

        $response = PersonApi::approvePersonRequest($this->id ?? $this->patientRequest->patient['id'], $requestData);

        if ($response['status'] !== 'APPROVED') {
            $this->dispatch('flashMessage', [
                'message' => 'Виникла помилка, зверніться до адміністратора.',
                'type' => 'error'
            ]);
        }

        if ($response['status'] === 'APPROVED') {
            $this->personRequestRepository->updatePersonRequestStatusByUuid($response);
            $this->isApproved = true;
        }
    }

    /**
     * Build and send API request 'Sign Person v2' and redirect to page if data is validated.
     *
     * @return void
     * @throws ApiException|Throwable
     */
    public function signPerson(): void
    {
        $patientId = $this->id ?? $this->patientRequest->patient['id'];

        $getPatientById = PersonApi::getCreatedPersonById($patientId);
        unset($getPatientById['meta'], $getPatientById['urgent']);
        $getPatientById['data']['patient_signed'] = $this->isInformed;

        // encrypt data
        $encryptedRequestData = schemaService()->requestSchemaNormalize($getPatientById['data'], app(PersonApi::class),
            'encryptSignSchemaRequest');
        // TODO: після мержа затестити чи пропадають ключі в яких значення false
        $encryptedRequestData['person']['no_tax_id'] = $this->noTaxId;
        $base64EncryptedData = $this->sendEncryptedData($encryptedRequestData, Auth::user()->tax_id);

        // sign person request
        $preRequest = [
            'signed_content' => $base64EncryptedData
        ];
        $signRequestData = schemaService()->requestSchemaNormalize($preRequest, app(PersonApi::class),
            'signSchemaRequest');
        $signResponse = PersonApi::singPersonRequest($patientId, $signRequestData, Auth::user()->tax_id);

        if ($signResponse['status'] !== 'SIGNED') {
            $this->dispatch('flashMessage', [
                'message' => 'Виникла помилка, зверніться до адміністратора.',
                'type' => 'error',
            ]);
        }

        if ($signResponse['status'] === 'SIGNED') {
            // create related person, update status
            $personSaved = $this->personRepository->savePersonResponseData($getPatientById, $signResponse['person_id']);
            $statusUpdated = $this->personRequestRepository->updatePersonRequestStatusByUuid($signResponse);
            $relationCreated = $this->personRequestRepository->createRelation($signResponse);

            if (!$personSaved || !$statusUpdated || !$relationCreated) {
                $this->dispatch('flashMessage', [
                    'message' => 'Виникла помилка, зверніться до адміністратора.',
                    'type' => 'error',
                ]);

                return;
            }

            to_route('patient.index')->with('flashMessage', [
                'message' => 'Пацієнт успішно створений',
                'type' => 'success',
            ]);
        }
    }

    /**
     * @throws \App\Classes\Cipher\Exceptions\ApiException
     */
    public function setCertificateAuthority(): array|null
    {
        return $this->getCertificateAuthority = $this->getCertificateAuthority();
    }

    public function updatedFile(): void
    {
        $this->keyContainerUpload = $this->file;
    }

    /**
     * Set empty string to taxId if patient refused to provide РНОКПП/ІПН
     *
     * @param  bool  $noTaxId
     * @return void
     */
    public function updatedNoTaxId(bool $noTaxId): void
    {
        if ($noTaxId) {
            $this->patientRequest->patient['taxId'] = '';
        }
    }

    /**
     * Resend SMS with confirmation code.
     *
     * @return void
     * @throws ApiException
     */
    public function resendSms(): void
    {
        if ($this->resendCooldown > 0) {
            return;
        }

        $response = PersonApi::resendAuthorizationSms($this->id ?? $this->patientRequest->patient['id']);

        if ($response['status'] === 'new') {
            $this->dispatch('flashMessage', [
                'message' => __('SMS успішно надіслано!'),
                'type' => 'success'
            ]);

            $this->resendCooldown = 60;
        }
    }

    /**
     * Build and send API request for create person
     *
     * @param  array  $patientData
     * @return array
     * @throws ApiException
     */
    protected function sendPersonRequest(array $patientData): array
    {
        $patientData['patient']['documents'] = $patientData['documents'];
        $patientData['patient']['addresses'][] = $patientData['addresses'];
        $patientData['patient']['confidantPerson']['personId'] = $patientData['documentsRelationship']['personId'];
        unset($patientData['documentsRelationship']['personId']);
        $patientData['patient']['confidantPerson']['documentsRelationship'] = $patientData['documentsRelationship'];

        $preRequest = [
            'person' => $patientData['patient']
        ];
        $filtered = removeEmptyKeys($preRequest);

        $patientRequest = schemaService()->requestSchemaNormalize($filtered, app(PersonApi::class));
        $patientRequest['person']['no_tax_id'] = $this->noTaxId;
        $patientRequest['patient_signed'] = false;
        $patientRequest['process_disclosure_data_consent'] = true;
        unset($patientRequest['person']['id']);

        return PersonApi::createPersonRequest($patientRequest);
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
                $this->documents = $patientData[$this->requestId]['documents'] ?? [];
                $this->documentsRelationship = $patientData[$this->requestId]['documentsRelationship'] ?? [];
                $this->uploadedDocuments = $patientData[$this->requestId]['uploadedDocuments'] ?? [];

                if (!empty($this->patient->patient)) {
                    $this->patientRequest->fill(
                        [
                            'patient' => $this->patient->patient,
                            'documents' => $this->patient->documents ?? [],
                            'addresses' => $this->patient->addresses ?? [],
                            'documentsRelationship' => $this->patient->documentsRelationship ?? [],
                            'uploadedDocuments' => $this->patient->uploadedDocuments ?? [],
                        ]
                    );
                }
            }
        }
    }

    /**
     * Initialize the edit mode for a specific model.
     *
     * @param  string  $model  The model type to initialize for editing.
     * @param  int  $keyProperty  The key property used to identify the specific item to edit.
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
     * @param  string  $model  The model type to update the data for.
     * @param  int  $keyProperty  The key property used to identify the specific item to update (optional).
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
     * @param  array  $addressData  An associative array containing address data for the patient.
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
     * @param  string  $key  The key under which the data should be stored in the cache (e.g., 'addresses').
     * @param  array  $data  The data to be stored in the cache.
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
     * @param  string  $model  The model type to update the data for.
     * @param  int  $keyProperty  The key property used to identify the specific item to update.
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
        $this->getPatient();
    }

    /**
     * Update the cached data for a specific model and key property.
     *
     * @param  string  $model  The model type to update the data for.
     * @param  int  $keyProperty  The key property used to identify the specific item to update.
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
     * @param  string|null  $model  The model type to reset the data for (optional).
     * @return void
     */
    public function closeModalModel(string $model = null): void
    {
        if (!empty($model)) {
            $this->patientRequest->{$model} = [];
        }

        $this->closeModal();
    }
}
