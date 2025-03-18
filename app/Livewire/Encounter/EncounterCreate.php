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
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

    /**
     * Patient first name.
     * @var string
     */
    public string $firstName;

    /**
     * Patient last name.
     * @var string
     */
    public string $lastName;

    /**
     * Patient second name.
     * @var string|null
     */
    public ?string $secondName = null;

    /**
     * Employee full name.
     * @var string
     */
    public string $performerFullName;

    public array $divisions;
    public array $allEpisodes;

    /**
     * KEP key.
     * @var object|null
     */
    public ?object $file = null;

    public array $dictionaryNames = [
        'eHealth/encounter_statuses',
        'eHealth/encounter_classes',
        'eHealth/encounter_types',
        'eHealth/encounter_priority',
        'eHealth/episode_types',
        'eHealth/ICPC2/condition_codes',
        'eHealth/diagnosis_roles',
        'eHealth/condition_clinical_statuses',
        'eHealth/condition_verification_statuses',
        'eHealth/condition_severities'
    ];

    /**
     * Is show diagnose fields.
     * @var bool
     */
    public bool $showDiagnose = false;

    // TODO: ask how to do and search for this problem: use public accessor or create getter
    public string $encounterUuid;
    public string $diagnoseUuid;
    protected string $legalEntityType;
    protected string $employeeType;

    /**
     * To track which diagnosis we are editing.
     * @var int
     */
    public int $currentIndex;

    public function render(): View
    {
        return view('livewire.encounter.encounter');
    }

    public string $query = '';
    public array $results = [];

    public function handleInput(string $value): void
    {
        $this->query = $value;

        $this->results = DB::table('icd_10')
            ->where('code', 'ILIKE', "%$this->query%")
            ->orWhere('description', 'ILIKE', "%$this->query%")
            ->limit(50)
            ->get()
            ->toArray();
    }

    public function mount(int $id): void
    {
        $this->id = $id;
        $this->setUuids();
        $this->setDefaultDate();

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
        $approvedEpisodesData = PatientApi::getApprovedEpisodes($this->uuid, $buildSearchRequest);
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
        $conditionsData = PatientApi::getConditions($this->uuid, $buildSearchRequest);
    }

    public function createDiagnose(): void
    {
        $defaults = $this->form->getDefaultCondition();
        $this->form->conditions = array_replace_recursive($this->form->conditions, $defaults);

        $this->createCondition();

        // reset fields
//        $this->form->encounter['diagnoses'] = [];
//        $this->form->conditions = [];
        $this->reset($this->form->encounter['diagnoses'] ,$this->form->conditions);
        $this->form->conditions['onsetTime'] = CarbonImmutable::now()->format('H:i');
        $this->form->conditions['assertedTime'] = CarbonImmutable::now()->format('H:i');

        $this->showDiagnose = false;
    }

    /**
     * Edit diagnoses fields by provided index.
     *
     * @param  int  $index
     * @return void
     */
    public function editDiagnose(int $index): void
    {
        $this->currentIndex = $index;

        $this->form->encounter['diagnoses'] = $this->allEpisodes[$index]['diagnoses'];
        $this->form->conditions = $this->allEpisodes[$index]['conditions'];
        $this->showDiagnose = true;
    }

    /**
     * Save diagnose changes to the table.
     *
     * @return void
     */
    public function saveDiagnoseChange(): void
    {
        $this->allEpisodes[$this->currentIndex] = [
            'diagnoses' => $this->form->encounter['diagnoses'],
            'conditions' => $this->form->conditions
        ];
        $this->showDiagnose = false;
    }

    /**
     * Delete diagnose from the table by index.
     *
     * @param  int  $index
     * @return void
     */
    public function destroyDiagnose(int $index): void
    {
        unset($this->allEpisodes[$index]);
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
//        $this->form->rulesForModelValidate($models);

//        $episodeUuid = Str::uuid()->toString();
//        $this->createEpisode($episodeUuid);

        // TODO: додати перевірку на унікальність uuid, трішки потім. uuid має бути унікальний для пацієнта а не унікальним в цілому?
        $this->form->encounter['id'] = $this->encounterUuid;
        $this->form->encounter['visit']['identifier']['value'] = Str::uuid()->toString();
//        $this->form->encounter['episode']['identifier']['value'] = $episodeUuid;
        $this->form->encounter['diagnoses']['condition']['identifier']['value'] = $this->diagnoseUuid;

//        $encounterForm = $this->updatePeriodDate($this->form->encounter);
        $preRequest = schemaService()
//            ->setDataSchema(['encounter' => $encounterForm], app(PatientApi::class))
            ->setDataSchema(['encounter' => $this->form->encounter], app(PatientApi::class))
            ->requestSchemaNormalize()
            ->getNormalizedData();

        // get conditions
        $preRequest['conditions'] = array_map(
            static fn(array $episode) => $episode['conditions'],
            $this->allEpisodes
        );

        $preRequest['conditions'] = array_map(
            static function (array $condition) {
                // convert dates
                $condition['onsetDate'] = convertToISO8601($condition['onsetDate'] . $condition['onsetTime']);
                $condition['assertedDate'] = convertToISO8601($condition['assertedDate'] . $condition['assertedTime']);
                unset($condition['onsetTime'], $condition['assertedTime']);

                return $condition;
            },
            $preRequest['conditions']
        );

        dd($preRequest['conditions']);

        //        $this->form->conditions['context']['identifier']['value'] = $this->encounterUuid;
//
        $this->form->conditions['onsetDate'] = convertToISO8601(
            $this->form->conditions['onsetDate'] . $this->form->conditions['onsetTime']
        );
        $this->form->conditions['assertedDate'] = convertToISO8601(
            $this->form->conditions['assertedDate'] . $this->form->conditions['assertedTime']
        );
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

    private function createCondition()
    {
        // закоментив, бо не потрібно при взаємодії із таблицею, робити це вже при запиті до АПІ!
        $this->form->conditions['id'] = $this->diagnoseUuid;

//        $this->form->conditions['evidences']['codes']['coding'][0]['code'] = $this->form->conditions['code']['coding'][0]['code'];
        // ID of existing observation in MedicalEvents.Observations or one of $.observations[*]
        // OR is an ID of existing condition in MedicalEvents.Conditions
//        $this->form->conditions['evidences']['details']['identifier']['value'] = $observationUuid;

        // TODO: https://e-health-ua.atlassian.net/wiki/spaces/EH/pages/17061216257/Submit+Encounter+Package#Validate-Conditions
        // 11 and 12
        $this->form->conditions['asserter']['identifier']['value'] = Employee::find(1)->uuid;

//        $this->allEpisodes[] = array_merge($this->form->conditions, $this->form->encounter['diagnoses']);
        $this->allEpisodes[] = [
            'conditions' => $this->form->conditions,
            'diagnoses' => $this->form->encounter['diagnoses']
        ];
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

    private function setUuids(): void
    {
        $this->encounterUuid = Str::uuid()->toString();
        $this->diagnoseUuid = Str::uuid()->toString();
    }

    private function setDefaultDate(): void
    {
        $nowTime = CarbonImmutable::now()->format('H:i');

        $this->form->encounter['period']['start'] = $nowTime;
        $this->form->encounter['period']['end'] = CarbonImmutable::now()->addMinutes(15)->format('H:i');
        $this->form->conditions['onsetTime'] = $nowTime;
        $this->form->conditions['assertedTime'] = $nowTime;
    }
}
