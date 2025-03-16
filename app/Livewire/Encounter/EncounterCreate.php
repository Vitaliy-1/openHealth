<?php

declare(strict_types=1);

namespace App\Livewire\Encounter;

use App\Classes\Cipher\Traits\Cipher;
use App\Classes\eHealth\Api\PatientApi;
use App\Classes\eHealth\Api\ServiceRequestApi;
use App\Classes\eHealth\Exceptions\ApiException;
use App\Livewire\Encounter\Forms\Api\EncounterRequestApi;
use App\Livewire\Encounter\Forms\Encounter as EncounterForm;
use App\Models\Employee\Employee;
use App\Models\Person\Person;
use App\Traits\FormTrait;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;
use Str;

class EncounterCreate extends Component
{
    use FormTrait, Cipher, WithFileUploads;

    public EncounterForm $form;

    #[Locked]
    public int $id;
    public string $uuid;
    public string $firstName;
    public string $lastName;
    public ?string $secondName = null;
    public string $performerFullName;

    public array $divisions;

    /**
     * Basic info about a patient from a search.
     * @var array
     */
    public array $patient;

    /**
     * KEP key.
     * @var object|null
     */
    public ?object $file = null;

    public array $dictionaries_field = [
        'eHealth/resources',
        'eHealth/encounter_statuses',
        'eHealth/encounter_classes',
        'eHealth/encounter_types',
        'eHealth/encounter_priority',
        'eHealth/episode_types',
        'eHealth/ICPC2/condition_codes',
        'eHealth/diagnosis_roles',
        'eHealth/condition_clinical_statuses',
        'eHealth/condition_verification_statuses'
    ];

    protected string $legalEntityType;
    protected string $employeeType;

    public function render(): View
    {
        return view('livewire.encounter.encounter');
    }

    public function mount(int $id): void
    {
        $this->id = $id;
        $this->loadPatientData();
        $this->getEmployeePartyData();
        $this->getDictionary();

        $this->adjustEpisodeTypes();
        $this->adjustEncounterClasses();
        $this->adjustEncounterTypes();

        $this->getDivisionData();
        $this->setCertificateAuthority();
    }

    /**
     * Search for referral number.
     *
     * @return void
     * @throws ApiException
     */
    public function searchForReferralNumber(): void
    {
        $buildSearchRequest = EncounterRequestApi::buildGetServiceRequestList($this->form->referralNumber);
        $requisitionData = ServiceRequestApi::searchForServiceRequestsByParams($buildSearchRequest);
    }

    /**
     * Search approved episode.
     *
     * @return void
     * @throws ApiException
     */
    public function searchForEpisode(): void
    {
        $buildSearchRequest = EncounterRequestApi::buildGetApprovedEpisodes();
        $approvedEpisodesData = PatientApi::getApprovedEpisodes($this->patient['id'], $buildSearchRequest);
    }

    /**
     * Search conditions.
     *
     * @return void
     * @throws ApiException
     */
    public function searchForConditions(): void
    {
        $buildSearchRequest = EncounterRequestApi::buildGetConditions();
        $conditionsData = PatientApi::getConditions($this->patient['id'], $buildSearchRequest);
    }

    /**
     * Validate and save data.
     *
     * @param  array  $models
     * @return void
     * @throws ValidationException
     */
    public function save(array $models)
    {
        $this->form->rulesForModelValidate($models);

        $episodeUuid = Str::uuid()->toString();
        $this->createEpisode($episodeUuid);

        // TODO: додати перевірку на унікальність uuid, трішки потім. uuid має бути унікальний для пацієнта а не унікальним в цілому?
        $this->form->encounter['id'] = Str::uuid()->toString();
        $this->form->encounter['visit']['identifier']['value'] = Str::uuid()->toString();
        $this->form->encounter['episode']['identifier']['value'] = $episodeUuid;

        $encounterForm = $this->updatePeriodDate($this->form->encounter);
        $preRequest = schemaService()
            ->setDataSchema(['encounter' => $encounterForm], app(PatientApi::class))
            ->requestSchemaNormalize()
            ->getNormalizedData();
        dd($preRequest);
    }

    /**
     * Submit encrypted data about person encounter.
     *
     * @return void
     * @throws ApiException
     */
    public function signPerson()
    {
        $encounterForm = $this->updatePeriodDate($this->form->encounter);
        $preRequest = schemaService()
            ->setDataSchema(['encounter' => $encounterForm], app(PatientApi::class))
            ->requestSchemaNormalize()
            ->getNormalizedData();

        $base64EncryptedData = $this->sendEncryptedData($preRequest, Auth::user()->tax_id);

        $prepareSubmitEncounter = [
            'visit' => (object) [
                'id' => $preRequest['encounter']['visit']['identifier']['value'],
                'period' => (object) [
                    'end' => $preRequest['encounter']['period']['end'],
                    'start' => $preRequest['encounter']['period']['start']
                ]
            ],
            'signed_data' => $base64EncryptedData,
        ];

        $submitEncounter = PatientApi::submitEncounter($this->uuid, $prepareSubmitEncounter);
    }

    /**
     * Open modal by provided model name.
     *
     * @param  string  $model
     * @return void
     */
    public function create(string $model): void
    {
        $this->openModal($model);
    }

    /**
     * Create episode for patient.
     *
     * @param  string  $episodeUuid
     * @return void
     */
    private function createEpisode(string $episodeUuid): void
    {
        $this->form->episode['id'] = $episodeUuid;
        $this->form->episode['managing_organization']['identifier']['value'] = Auth::user()->legalEntity->uuid;
        $this->form->episode['period']['start'] = $this->form->encounter['period']['date'] . 'T' . $this->form->encounter['period']['start'] . ':00' . date('P');

        $preEpisodeRequest = schemaService()
            ->setDataSchema($this->form->episode, app(PatientApi::class))
            ->requestSchemaNormalize('schemaEpisodeRequest')
            ->getNormalizedData();

        try {
            $createEpisode = PatientApi::createEpisode($this->uuid, $preEpisodeRequest);
        } catch (ApiException) {
            $this->dispatch('flashMessage', [
                'message' => __('Виникла помилка при створенні епізоду. Зверніться до адміністратора.'),
                'type' => 'error'
            ]);
        }

        dd($createEpisode);
    }

    private function loadPatientData(): void
    {
        $patient = Person::select(['uuid', 'first_name', 'last_name', 'second_name'])
            ->where('id', $this->id)
            ->first()
            ?->toArray();

        $this->uuid = $patient['uuid'];
        $this->firstName = $patient['first_name'];
        $this->lastName = $patient['last_name'];
        $this->secondName = $patient['second_name'] ?? null;
        $this->legalEntityType = Auth::user()->legalEntity->type;
        // TODO: брати із Auth, коли буде відповідна структура в БД
        $this->employeeType = Employee::find(1)->employee_type;
    }

    protected function getEmployeePartyData(): void
    {
        // TODO: потім взяти employee авторизованого
        $employee = Employee::find(1);
        $party = $employee->party;

        $this->performerFullName = $party->last_name . ' ' . $party->first_name . ' ' . $party->second_name;

        $this->form->encounter['performer']['identifier']['value'] = $employee->uuid;
        $this->form->episode['care_manager']['identifier']['value'] = $employee->uuid;
    }

    protected function getDivisionData(): void
    {
        $this->divisions = Auth::user()->legalEntity->division->toArray();
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

    public function updatedFile(): void
    {
        $this->keyContainerUpload = $this->file;
    }

    private function updatePeriodDate(array $encounterForm): array
    {
        $encounterForm['period']['start'] = $encounterForm['period']['date'] . 'T' . $encounterForm['period']['start'] . ':00' . date('P');
        $encounterForm['period']['end'] = $encounterForm['period']['date'] . 'T' . $encounterForm['period']['end'] . ':00' . date('P');
        unset($encounterForm['period']['date']);

        return $encounterForm;
    }

    /**
     * Adjust episode types according to legal entity type and employee type.
     *
     * @return void
     */
    private function adjustEpisodeTypes(): void
    {
        $allowedValues = $this->getAllowedValues(
            'ehealth.legal_entity_episode_types',
            'ehealth.employee_episode_types'
        );
        $this->adjustDictionary('eHealth/episode_types', $allowedValues);
    }

    /**
     * Show encounter classes based on legal entity and employee type.
     *
     * @return void
     */
    private function adjustEncounterClasses(): void
    {
        $allowedValues = $this->getAllowedValues(
            'ehealth.legal_entity_encounter_classes',
            'ehealth.employee_encounter_classes'
        );
        $this->adjustDictionary('eHealth/encounter_classes', $allowedValues);
    }

    /**
     * Show encounter types based on encounter class.
     *
     * @return void
     */
    private function adjustEncounterTypes(): void
    {
        $allowedValues = config('ehealth.encounter_class_encounter_types')[key($this->dictionaries['eHealth/encounter_classes'])];
        $this->adjustDictionary('eHealth/encounter_types', $allowedValues);
    }

    /**
     * Get allowed values.
     *
     * @param  string  $configKey
     * @param  string|null  $additionalConfigKey
     * @return array
     */
    private function getAllowedValues(string $configKey, ?string $additionalConfigKey = null): array
    {
        $allowedValues = config($configKey);

        if ($additionalConfigKey) {
            $additionalValues = config($additionalConfigKey);
            $allowedValues = array_intersect(
                $allowedValues[$this->legalEntityType],
                $additionalValues[$this->employeeType]
            );
        }

        return $allowedValues;
    }

    /**
     * Adjust dictionaries by provided key and values.
     *
     * @param  string  $dictionaryKey
     * @param  array  $allowedValues
     * @return void
     */
    private function adjustDictionary(string $dictionaryKey, array $allowedValues): void
    {
        $this->dictionaries[$dictionaryKey] = Arr::only($this->dictionaries[$dictionaryKey], $allowedValues);
    }
}
