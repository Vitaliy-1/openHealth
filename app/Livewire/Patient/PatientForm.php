<?php

declare(strict_types=1);

namespace App\Livewire\Patient;

use App\Classes\Cipher\Traits\Cipher;
use App\Classes\eHealth\Api\PersonApi;
use App\Classes\eHealth\Api\PersonRequestApi;
use App\Classes\eHealth\Exceptions\ApiException;
use App\Livewire\Patient\Forms\Api\PatientRequestApi;
use App\Livewire\Patient\Forms\PatientFormRequest;
use App\Models\Person\Person;
use App\Models\Person\PersonRequest;
use App\Repositories\PersonRepository;
use App\Traits\AddressSearch;
use App\Traits\FormTrait;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;
use Throwable;

class PatientForm extends Component
{
    use FormTrait;
    use WithFileUploads;
    use Cipher;
    use AddressSearch;

    /**
     * Allowed model modals name.
     */
    private const array ALLOWED_MODAL_MODELS = [
        'signedContent'
    ];

    #[Locked]
    public int $patientId;

    public string $mode = 'create';
    public PatientFormRequest $form;

    /**
     * List of founded confidant person.
     * @var array
     */
    public array $confidantPerson = [];

    public array $uploadedDocuments = [];

    /**
     * Content that shows to the patient when signing the leaflet.
     * @var string
     */
    public string $leafletContent;

    /**
     * Check if the search person's request found someone.
     *
     * @var bool
     */
    public bool $searchPerformed = false;

    /**
     * ID selected confidant person.
     * @var string|null
     */
    public ?string $selectedConfidantPatientId = null;

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
     * Mark 'information from the leaflet was communicated to the patient'.
     * @var bool
     */
    public bool $isInformed = false;

    /**
     * Is patient incapable or child less than 14 y.o.
     * @var bool
     */
    public bool $isIncapacitated = false;

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

    public array $dictionaryNames = [
        'DOCUMENT_TYPE',
        'DOCUMENT_RELATIONSHIP_TYPE',
        'GENDER',
        'PHONE_TYPE'
    ];

    /**
     * Initialize the component with required data.
     *
     * @param  int|null  $id
     * @return void
     * @throws \App\Classes\Cipher\Exceptions\ApiException
     */
    public function mount(?int $id = null): void
    {
        if ($id !== null) {
            $fromDatabase = PersonRequest::find($id, ['id']);

            // Make sure the ID in the URL matches the patient's ID.
            if ($fromDatabase?->id !== $id) {
                abort(403);
            }

            $this->patientId = $id;
            $this->checkIfIncapacitated();
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
     * Initialize the creation mode for a specific model.
     *
     * @param  string  $model  The model type to initialize for creation.
     * @return void
     * @throws ValidationException
     */
    public function create(string $model): void
    {
        $this->validateModel($model);

        $this->mode = 'create';
        $this->form->{$model} = [];
        $this->openModal($model);
    }

    /**
     * Choose a confidant person from the provided list.
     *
     * @param  string  $id
     * @return void
     */
    public function chooseConfidantPerson(string $id): void
    {
        $patientData = collect($this->confidantPerson)->firstWhere('id', $id);

        if ($patientData) {
            $this->selectedConfidantPatientId = $id;
            $this->confidantPerson = [$patientData];
            $this->form->patient['authenticationMethods'][0]['value'] = $patientData['id'];
        }

        $this->searchPerformed = true;
    }

    /**
     * Remove selected confidant person from the cache and form.
     *
     * @return void
     */
    public function removeConfidantPerson(): void
    {
        $this->form->patient['authenticationMethods'][0]['value'] = null;

        $this->confidantPerson = [];
        $this->selectedConfidantPatientId = null;
        $this->searchPerformed = false;
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

        $buildSearchRequest = PatientRequestApi::buildSearchForPerson($this->form->patientsFilter);

        $this->confidantPerson = arrayKeysToCamel(PersonApi::searchForPersonByParams($buildSearchRequest));
        $this->searchPerformed = true;
    }

    /**
     * Send API request 'Create Person v2' and show the next page if data is validated.
     *
     * @return void
     * @throws ApiException|Throwable
     */
    public function createPerson(): void
    {
        if (!Auth::user()?->can('createPerson', Person::class)) {
            $this->dispatch('flashMessage', [
                'message' => 'У вас немає дозволу на створення пацієнта.',
                'type' => 'error'
            ]);

            return;
        }

        $this->preparePersonRequest();
        $this->validatePersonRequest(['patient', 'documents', 'documentsRelationship']);

        $response = $this->sendPersonRequest(removeEmptyKeys($this->form->toArray()));

        if ($response['meta']['code'] !== 201) {
            $this->dispatch('flashMessage', [
                'message' => 'Виникла помилка, зверніться до адміністратора.',
                'type' => 'error'
            ]);
        }

        if ($response['meta']['code'] === 201) {
            if (isset($this->patientId)) {
                $response['data']['dbId'] = $this->patientId;
            }

            if (isset($response['data']['person']['confidant_person'])) {
                $response['data']['person']['confidant_person']['confidantPersonInfo'] = arrayKeysToSnake($this->confidantPerson[0]);
            }
            // save in DB
            $personSaved = PersonRepository::savePersonResponseData($response['data'], PersonRequest::class);
            if (!$personSaved) {
                $this->dispatch('flashMessage', [
                    'message' => 'Виникла помилка, зверніться до адміністратора.',
                    'type' => 'error',
                ]);

                return;
            }

            $this->form->patient['id'] = $response['data']['id'];
            $this->uploadedDocuments = $response['urgent']['documents'];
            $this->viewState = 'new';
        }
    }

    /**
     * Create data about person request in DB.
     *
     * @return void
     * @throws Throwable
     */
    public function createApplication(): void
    {
        if (!Auth::user()?->can('createApplication', PersonRequest::class)) {
            $this->dispatch('flashMessage', [
                'message' => 'У вас немає дозволу на створення пацієнта.',
                'type' => 'error'
            ]);

            return;
        }

        $this->preparePersonRequest();
        $this->validatePersonRequest(['patient', 'documents', 'documentsRelationship']);

        $response = PersonRepository::savePersonResponseData(
            arrayKeysToSnake($this->form->toArray()),
            PersonRequest::class
        );

        if ($response === false) {
            $this->dispatch('flashMessage', [
                'message' => 'Виникла помилка, зверніться до адміністратора.',
                'type' => 'error'
            ]);

            return;
        }

        to_route('patient.index')->with('flashMessage', [
            'message' => 'Пацієнт успішно створений',
            'type' => 'success'
        ]);
    }

    /**
     * Validate uploaded files and save.
     *
     * @param  string  $field
     * @return void
     * @throws ValidationException
     */
    public function updated(string $field): void
    {
        if (str_starts_with($field, 'form.uploadedDocuments')) {
            $this->form->rulesForModelValidate('uploadedDocuments');
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
        unset($this->form->uploadedDocuments[$key]);
    }

    /**
     * Upload patient files to the appropriate URL.
     *
     * @return void
     * @throws ValidationException
     */
    public function sendFiles(): void
    {
        $this->form->rulesForModelValidate('uploadedDocuments');

        $totalFiles = count($this->form->uploadedDocuments);
        // Check that all provided files were uploaded
        if ($totalFiles !== count($this->uploadedDocuments)) {
            $this->dispatch('flashMessage', [
                'message' => 'Будь ласка завантажте всі файли!',
                'type' => 'error',
            ]);

            return;
        }

        $successCount = 0;
        foreach ($this->form->uploadedDocuments as $key => $document) {
            try {
                $requestData = PatientRequestApi::buildUploadFileRequest($document);
                $uploadResponse = PersonRequestApi::uploadFileRequest(
                    trim($this->uploadedDocuments[$key]['url']),
                    $requestData
                );

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
            } catch (Exception) {
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

        $response = PersonRequestApi::resendAuthorizationSms($this->form->patient['id']);

        if ($response['status'] === 'new') {
            $this->dispatch('flashMessage', [
                'message' => __('SMS успішно надіслано!'),
                'type' => 'success'
            ]);

            $this->resendCooldown = 60;
        }
    }

    /**
     * Build and send API request 'Approve Person v2' and show the next page if data is validated.
     *
     * @return void
     * @throws Throwable
     */
    public function approvePerson(): void
    {
        try {
            $this->form->rulesForModelValidate('verificationCode');
        } catch (ValidationException $e) {
            $this->dispatch('flashMessage', [
                'message' => 'Помилка валідації.',
                'type' => 'error'
            ]);

            throw $e;
        }

        $preRequest = [
            'verification_code' => (int) $this->form->verificationCode
        ];
        $requestData = schemaService()
            ->setDataSchema($preRequest, app(PersonRequestApi::class))
            ->requestSchemaNormalize('approveSchemaRequest')
            ->getNormalizedData();

        $response = PersonRequestApi::approvePersonRequest($this->form->patient['id'], $requestData);

        if ($response['status'] !== 'APPROVED') {
            $this->dispatch('flashMessage', [
                'message' => 'Виникла помилка, зверніться до адміністратора.',
                'type' => 'error'
            ]);
        }

        if ($response['status'] === 'APPROVED') {
            PersonRepository::updatePersonRequestStatusByUuid($response);
            $this->isApproved = true;

            // save a leaflet and open the modal
            $this->leafletContent = $response['content'];
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

    public function updatedFile(): void
    {
        $this->keyContainerUpload = $this->file;
    }

    /**
     * Build and send API request 'Sign Person v2' and redirect to page if data is validated.
     *
     * @return void
     * @throws ApiException|Throwable
     */
    public function signPerson(): void
    {
        $getPatientById = PersonRequestApi::getCreatedPersonById($this->form->patient['id']);
        unset($getPatientById['meta'], $getPatientById['urgent']);
        $getPatientById['data']['patient_signed'] = $this->isInformed;

        // encrypt data
        $encryptedRequestData = schemaService()
            ->setDataSchema($getPatientById['data'], app(PersonRequestApi::class))
            ->requestSchemaNormalize('encryptSignSchemaRequest')
            ->getNormalizedData();

        $base64EncryptedData = $this->sendEncryptedData($encryptedRequestData, Auth::user()->tax_id);

        // sign person request
        $preRequest = [
            'signed_content' => $base64EncryptedData
        ];
        $signRequestData = schemaService()
            ->setDataSchema($preRequest, app(PersonRequestApi::class))
            ->requestSchemaNormalize('signSchemaRequest')
            ->getNormalizedData();

        $signResponse = PersonRequestApi::singPersonRequest(
            $this->form->patient['id'],
            $signRequestData,
            Auth::user()->tax_id
        );

        if ($signResponse['status'] !== 'SIGNED') {
            $this->dispatch('flashMessage', [
                'message' => 'Виникла помилка, зверніться до адміністратора.',
                'type' => 'error',
            ]);
        }

        if ($signResponse['status'] === 'SIGNED') {
            // create related person, update status
            $personSaved = PersonRepository::savePersonResponseData(
                $getPatientById['data'],
                Person::class,
                $signResponse['person_id']
            );
            $statusUpdated = PersonRepository::updatePersonRequestStatusByUuid($signResponse);
            $relationCreated = PersonRepository::createRelation($signResponse);

            if (!$personSaved || !$statusUpdated || !$relationCreated) {
                $this->dispatch('flashMessage', [
                    'message' => 'Виникла помилка, зверніться до адміністратора.',
                    'type' => 'error'
                ]);

                return;
            }

            to_route('patient.index')->with('flashMessage', [
                'message' => 'Пацієнт успішно створений',
                'type' => 'success'
            ]);
        }
    }

    /**
     * Check if the patient has a related confidant person.
     *
     * @return void
     */
    protected function checkIfIncapacitated(): void
    {
        $this->isIncapacitated = PersonRequest::where('id', $this->patientId)
            ->whereHas('confidantPerson')
            ->exists();
    }

    /**
     * Get all data about the patient from the DB.
     *
     * @return void
     */
    protected function getPatient(): void
    {
        if (isset($this->patientId)) {
            $patientData = PersonRequest::showPersonRequest($this->patientId);
            $this->form->fill($patientData);
            $this->address = $patientData['address'];
            $this->confidantPerson = $patientData['confidantPerson'] ?? [];
        }
    }

    /**
     * Get Certificate Authority from API.
     *
     * @return array
     * @throws \App\Classes\Cipher\Exceptions\ApiException
     */
    private function setCertificateAuthority(): array
    {
        return $this->getCertificateAuthority = $this->getCertificateAuthority();
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
        $patientData['patient']['documents'] = $patientData['documents'];
        $patientData['patient']['addresses'][] = $patientData['addresses'];

        if (isset($patientData['patient']['id'])) {
            unset($patientData['patient']['id']);
        }

        if (!empty($patientData['confidantPerson'])) {
            $patientData['patient']['confidantPerson']['personId'] = $patientData['confidantPerson'][0]['personUuid'] ?? $patientData['confidantPerson'][0]['id'];
            $patientData['patient']['confidantPerson']['documentsRelationship'] = $patientData['documentsRelationship'];
        }

        $preRequest = schemaService()
            ->setDataSchema(['person' => $patientData['patient']], app(PersonRequestApi::class))
            ->requestSchemaNormalize()
            ->getNormalizedData();

        $preRequest['patient_signed'] = $this->isInformed;
        $preRequest['process_disclosure_data_consent'] = true;

        return PersonRequestApi::createPersonRequest($preRequest);
    }

    /**
     * Prepare person request data.
     *
     * @return void
     */
    private function preparePersonRequest(): void
    {
        $this->form->addresses = $this->address;
        $this->form->confidantPerson = $this->confidantPerson;
    }

    /**
     * Validate person request data.
     *
     * @param  array  $models
     * @throws ValidationException
     */
    private function validatePersonRequest(array $models): void
    {
        try {
            $this->form->rulesForModelValidate($models);
            $this->form->validateBeforeSendApi();
        } catch (ValidationException $e) {
            $this->dispatch('flashMessage', [
                'message' => $e->validator->errors()->first(),
                'type' => 'error'
            ]);

            throw $e;
        }
    }

    /**
     * Validate model name from modals.
     *
     * @param  string  $model
     * @return void
     * @throws ValidationException
     */
    private function validateModel(string $model): void
    {
        if (!in_array($model, self::ALLOWED_MODAL_MODELS, true)) {
            $this->dispatch('flashMessage', [
                'message' => 'Недопустиме значення моделі',
                'type' => 'error'
            ]);

            throw ValidationException::withMessages([
                'model' => 'Недопустиме значення моделі'
            ]);
        }
    }
}
