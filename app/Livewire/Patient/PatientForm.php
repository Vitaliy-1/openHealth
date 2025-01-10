<?php

namespace App\Livewire\Patient;

use App\Classes\Cipher\Traits\Cipher;
use App\Classes\eHealth\Api\PersonApi;
use App\Classes\eHealth\Exceptions\ApiException;
use App\Livewire\Patient\Forms\Api\PatientRequestApi;
use App\Livewire\Patient\Forms\PatientFormRequest;
use App\Models\Person\Person;
use App\Models\Person\PersonRequest;
use App\Repositories\PersonRepository;
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

    protected PersonRepository $personRepository;
    protected string $patientCacheKey;
    public string $patientId;

    public array $documents = [];
    public array $documentsRelationship = [];
    public string $mode = 'create';
    public PatientFormRequest $patientRequest;
    public Person $patient;
    public array $uploadedDocuments = [];

    /**
     * Content that shows to the patient when signing the leaflet.
     * @var string
     */
    public string $leafletContent;
    public string $requestId;
    public int $keyProperty;
    public string $viewState = 'default';

    /**
     * Track uploaded files.
     * @var array
     */
    public array $uploadedFiles = [];

    /**
     * Check is files was successfully uploaded.
     * @var bool
     */
    public bool $isUploaded = false;

    /**
     * Check is store patient was successful.
     * @var bool
     */
    public bool $isPatientStored = false;

    /**
     * Mark 'information from the leaflet was communicated to the patient'.
     * @var bool
     */
    public bool $isInformed = false;

    /**
     * Check is patient refused to provide RNOCPP/IPN.
     * @var bool
     */
    public bool $noTaxId = false;

    /**
     * Is patient incapable or child less than 14 y.o.
     * @var bool
     */
    public bool $isIncapable = false;

    /**
     * Check is person approved.
     * @var bool
     */
    public bool $isApproved = false;

    /**
     * KEP key.
     * @var object|null
     */
    public ?object $file = null;

    /**
     * Time to resend SMS in seconds.
     * @var int
     */
    public int $resendCooldown = 60;

    protected $listeners = ['addressDataFetched'];

    public array $dictionaries_field = [
        'DOCUMENT_TYPE',
        'DOCUMENT_RELATIONSHIP_TYPE',
        'GENDER',
        'PHONE_TYPE'
    ];

    /**
     * Boot the component with required dependencies.
     *
     * @param  PersonRepository  $personRepository
     * @return void
     */
    public function boot(PersonRepository $personRepository): void
    {
        $this->personRepository = $personRepository;
        $this->patientCacheKey = self::CACHE_PREFIX . '-' . Auth::user()->legalEntity->uuid;
    }

    /**
     * Initialize the component with required data.
     *
     * @param  Request  $request
     * @param  string  $id
     * @return void
     * @throws \App\Classes\Cipher\Exceptions\ApiException
     */
    public function mount(Request $request, string $id = ''): void
    {
        if ($request->has('storeId')) {
            $this->requestId = $request->input('storeId');
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

    /**
     * Save the chosen confidant person to cache and form.
     *
     * @param  array  $patientData
     * @return void
     */
    #[On('confidant-person-selected')]
    public function confidantPersonSelected(array $patientData): void
    {
        $cacheData = $this->getCache($this->patientCacheKey) ?? [];

        $cacheData[$this->requestId]['documentsRelationship']['personId'] = $patientData['id'];
        $cacheData[$this->requestId]['patient']['authenticationMethods'][0]['value'] = $patientData['id'];
        $cacheData[$this->requestId]['patient']['confidantPersonInfo'] = $patientData;

        $this->patientRequest->documentsRelationship['personId'] = $patientData['id'];
        $this->patientRequest->patient['authenticationMethods'][0]['value'] = $patientData['id'];
        $this->patientRequest->patient['confidantPersonInfo'] = $patientData;

        $this->putCache($this->patientCacheKey, $cacheData);
    }

    /**
     * Remove the confidant person from the cache and form.
     *
     * @return void
     */
    #[On('confidant-person-removed')]
    public function confidantPersonRemoved(): void
    {
        $cacheData = $this->getCache($this->patientCacheKey) ?? [];

        unset($cacheData[$this->requestId]['documentsRelationship'],
            $cacheData[$this->requestId]['patient']['authenticationMethods'],
            $cacheData[$this->requestId]['confidantPersonInfo']
        );

        $this->patientRequest->documentsRelationship['personId'] = null;
        $this->patientRequest->patient['authenticationMethods'][0]['value'] = null;
        $this->patientRequest->patient['confidantPersonInfo'] = null;

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
     * @throws Throwable
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

        $response = $this->sendPersonRequest(removeEmptyKeys($this->patientRequest->toArray()));

        if ($response['meta']['code'] !== 201) {
            $this->dispatch('flashMessage', [
                'message' => 'Виникла помилка, зверніться до адміністратора.',
                'type' => 'error'
            ]);
        }

        if ($response['meta']['code'] === 201) {
            $response['data']['person']['confidant_person']['confidantPersonInfo'] = $this->patientRequest->patient['confidantPersonInfo'];
            // save in DB
            $personSaved = $this->personRepository->savePersonResponseData($response['data'], PersonRequest::class);

            if (!$personSaved) {
                $this->dispatch('flashMessage', [
                    'message' => 'Виникла помилка, зверніться до адміністратора.',
                    'type' => 'error',
                ]);

                return;
            }

            $this->cleanAndUpdateCache($response);

            $this->uploadedDocuments = $response['urgent']['documents'];
            $this->viewState = 'new';
        }
    }

    /**
     * Validate uploaded files and save.
     *
     * @param  string  $propertyName
     * @return void
     * @throws ValidationException
     */
    public function updated(string $propertyName): void
    {
        if (str_starts_with($propertyName, 'patientRequest.uploadedDocuments')) {
            $this->patientRequest->rulesForModelValidate('uploadedDocuments');
            $this->uploadedDocuments = $this->patientRequest->uploadedDocuments;
        }
    }

    /**
     * Upload patient files to the appropriate URL.
     *
     * @param  string  $model
     * @return void
     * @throws ValidationException
     */
    public function sendFiles(string $model): void
    {
        $this->patientRequest->rulesForModelValidate($model);

        // Check that all files were uploaded
        $allDocumentsUploaded = collect($this->patientRequest->uploadedDocuments)
            ->every(fn($document) => isset($document['documentsRelationship']));

        if (!$allDocumentsUploaded) {
            $this->dispatch('flashMessage', [
                'message' => 'Будь ласка завантажте всі файли!',
                'type' => 'error',
            ]);

            return;
        }

        $successCount = 0;
        $totalFiles = count($this->patientRequest->uploadedDocuments);

        foreach ($this->patientRequest->uploadedDocuments as $key => $document) {
            if (!isset($document['documentsRelationship'])) {
                continue;
            }

            try {
                $requestData = PatientRequestApi::buildUploadFileRequest($document['documentsRelationship']);
                $uploadResponse = PersonApi::uploadFileRequest($document['url'], $requestData);

                if (isset($uploadResponse['status']) && $uploadResponse['status'] === 200) {
                    $successCount++;

                    $this->uploadedFiles[$key] = true;
                } else {
                    $this->dispatch('flashMessage', [
                        'message' => 'Виникла помилка, зверніться до адміністратора',
                        'type' => 'error',
                    ]);

                    $this->uploadedFiles[$key] = false;
                }
            } catch (\Exception $e) {
                $this->dispatch('flashMessage', [
                    'message' => 'Виникла помилка, зверніться до адміністратора',
                    'type' => 'error',
                ]);

                $this->uploadedFiles[$key] = false;
            }
        }

        // Show final status message
        if ($successCount === $totalFiles) {
            $this->isUploaded = true;

            $this->dispatch('flashMessage', [
                'message' => 'Всі файли успішно завантажено',
                'type' => 'success',
            ]);
        }
    }

    /**
     * Delete uploaded file.
     *
     * @param  int  $key
     * @return void
     */
    public function deleteDocument(int $key): void
    {
        unset($this->patientRequest->uploadedDocuments[$key]['documentsRelationship']);
        $this->uploadedDocuments = $this->patientRequest->uploadedDocuments;
    }

    /**
     * Build and send API request 'Approve Person v2' and show the next page if data is validated.
     *
     * @param  string  $model
     * @return void
     * @throws ValidationException|Throwable
     */
    public function approvePerson(string $model): void
    {
        $this->patientRequest->rulesForModelValidate($model);

        $preRequest = [
            'verification_code' => (int) $this->patientRequest->verificationCode
        ];
        $requestData = schemaService()
            ->setDataSchema($preRequest, app(PersonApi::class))
            ->requestSchemaNormalize('approveSchemaRequest')
            ->getNormalizedData();

        $cacheData = $this->getCache($this->patientCacheKey);
        $response = PersonApi::approvePersonRequest($cacheData[$this->requestId]['patient']['id'], $requestData);

        if ($response['status'] !== 'APPROVED') {
            $this->dispatch('flashMessage', [
                'message' => 'Виникла помилка, зверніться до адміністратора.',
                'type' => 'error'
            ]);
        }

        if ($response['status'] === 'APPROVED') {
            $this->personRepository->updatePersonRequestStatusByUuid($response);
            $this->isApproved = true;

            // save a leaflet and open the modal
            $this->leafletContent = $response['content'];
            $cacheData[$this->requestId]['content'] = $response['content'];
            $this->putCache($this->patientCacheKey, $cacheData);

            $this->openModal('patientLeaflet');
        }
    }

    /**
     * Inform the patient about processing his data and close the modal.
     *
     * @return void
     */
    public function informAndCloseModal(): void
    {
        $this->isInformed = true;
        $this->isApproved = true;
        $this->closeModalModel();
    }

    /**
     * Build and send API request 'Sign Person v2' and redirect to page if data is validated.
     *
     * @return void
     * @throws ApiException|Throwable
     */
    public function signPerson(): void
    {
        $cacheData = $this->getCache($this->patientCacheKey);
        $patientId = $cacheData[$this->requestId]['patient']['id'];

        $getPatientById = PersonApi::getCreatedPersonById($patientId);
        unset($getPatientById['meta'], $getPatientById['urgent']);
        $getPatientById['data']['patient_signed'] = $this->isInformed;

        // encrypt data
        $encryptedRequestData = schemaService()
            ->setDataSchema($getPatientById['data'], app(PersonApi::class))
            ->requestSchemaNormalize('encryptSignSchemaRequest')
            ->getNormalizedData();

        $base64EncryptedData = $this->sendEncryptedData($encryptedRequestData, Auth::user()->tax_id);

        // sign person request
        $preRequest = [
            'signed_content' => $base64EncryptedData
        ];
        $signRequestData = schemaService()
            ->setDataSchema($preRequest, app(PersonApi::class))
            ->requestSchemaNormalize('signSchemaRequest')
            ->getNormalizedData();

        $signResponse = PersonApi::singPersonRequest($patientId, $signRequestData, Auth::user()->tax_id);

        if ($signResponse['status'] !== 'SIGNED') {
            $this->dispatch('flashMessage', [
                'message' => 'Виникла помилка, зверніться до адміністратора.',
                'type' => 'error',
            ]);
        }

        if ($signResponse['status'] === 'SIGNED') {
            // create related person, update status
            $personSaved = $this->personRepository->savePersonResponseData(
                $getPatientById['data'],
                Person::class,
                $signResponse['person_id']
            );
            $statusUpdated = $this->personRepository->updatePersonRequestStatusByUuid($signResponse);
            $relationCreated = $this->personRepository->createRelation($signResponse);

            if (!$personSaved || !$statusUpdated || !$relationCreated) {
                $this->dispatch('flashMessage', [
                    'message' => 'Виникла помилка, зверніться до адміністратора.',
                    'type' => 'error'
                ]);

                return;
            }

            $this->forgetCache($this->patientCacheKey);

            to_route('patient.index')->with('flashMessage', [
                'message' => 'Пацієнт успішно створений',
                'type' => 'success'
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
     * Set empty string to taxId if patient refused to provide RNOCPP/IPN.
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

        $response = PersonApi::resendAuthorizationSms($this->patientRequest->patient['id']);

        if ($response['status'] === 'new') {
            $this->dispatch('flashMessage', [
                'message' => __('SMS успішно надіслано!'),
                'type' => 'success'
            ]);

            $this->resendCooldown = 60;
        }
    }

    /**
     * Build and send API request for create person.
     *
     * @param  array  $patientData
     * @return array
     * @throws ApiException
     */
    protected function sendPersonRequest(array $patientData): array
    {
        $patientData['patient']['noTaxId'] = $this->noTaxId;
        $patientData['patient']['documents'] = $patientData['documents'];
        $patientData['patient']['addresses'][] = $patientData['addresses'];

        if (!empty($patientData['documentsRelationship'])) {
            $patientData['patient']['confidantPerson']['personId'] = $patientData['documentsRelationship']['personId'];
            unset($patientData['documentsRelationship']['personId']);
            $patientData['patient']['confidantPerson']['documentsRelationship'] = $patientData['documentsRelationship'];
        }

        $preRequest = schemaService()
            ->setDataSchema(['person' => $patientData['patient']], app(PersonApi::class))
            ->requestSchemaNormalize()
            ->getNormalizedData();

        $preRequest['patient_signed'] = $this->isInformed;
        $preRequest['process_disclosure_data_consent'] = true;

        return PersonApi::createPersonRequest($preRequest);
    }

    /**
     * Get all data about the patient from the DB or cache.
     *
     * @return void
     */
    protected function getPatient(): void
    {
        if (isset($this->patientId)) {
            $patientData = PersonRequest::showPersonRequest($this->patientId);
            $this->patientRequest->fill($patientData);
            $this->documents = $patientData['documents'] ?? [];
            $this->documentsRelationship = $patientData['documentsRelationship'][0]['documentsRelationship'] ?? [];
        }

        if (isset($this->requestId) && $this->hasCache($this->patientCacheKey)) {
            $patientData = $this->getCache($this->patientCacheKey);
            if (isset($patientData[$this->requestId])) {
                $this->patientRequest->fill($patientData[$this->requestId]);
                $this->documents = $this->patientRequest->documents ?? [];
                $this->documentsRelationship = $this->patientRequest->documentsRelationship ?? [];
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
     * @param  string  $key  The key under which the data should be stored in the cache
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

    /**
     * Remove an item from the model by key in the cache.
     *
     * @param  string  $model
     * @param  string  $keyProperty
     * @return void
     */
    public function remove(string $model, string $keyProperty = ''): void
    {
        $cacheData = $this->getCache($this->patientCacheKey);

        if (isset($cacheData[$this->requestId][$model][$keyProperty])) {
            unset($cacheData[$this->requestId][$model][$keyProperty]);
        }

        $this->putCache($this->patientCacheKey, $cacheData);
        $this->getPatient();
    }

    /**
     * Cleans cache data keeping only uploaded documents and patient ID.
     *
     * @param  array  $response  API response
     * @return void
     */
    private function cleanAndUpdateCache(array $response): void
    {
        $cacheData = $this->getCache($this->patientCacheKey);

        $cacheData[$this->requestId] = [
            'uploadedDocuments' => $response['urgent']['documents'],
            'patient' => [
                'id' => $response['data']['id']
            ],
        ];

        $this->putCache($this->patientCacheKey, $cacheData);
    }
}
