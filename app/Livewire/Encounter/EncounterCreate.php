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
use App\Repositories\EncounterRepository;
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
    use FormTrait;
    use Cipher;
    use WithFileUploads;

    public EncounterForm $form;

    #[Locked]
    public int $patientId;
    #[Locked]
    public ?int $encounterId = null;
    public string $patientUuid;

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

    public array $divisions;

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
        'eHealth/ICPC2/reasons',
        'eHealth/ICPC2/actions',
        'eHealth/diagnosis_roles',
        'eHealth/condition_clinical_statuses',
        'eHealth/condition_verification_statuses',
        'eHealth/condition_severities',
        'eHealth/report_origins',
        'eHealth/reason_explanations',
        'eHealth/reason_not_given_explanations',
        'eHealth/immunization_report_origins'
    ];

    public string $encounterUuid;
    public array $diagnoseUuids;
    public string $visitUuid;
    public string $episodeUuid;
    protected string $legalEntityType;
    protected string $employeeType;

    /**
     * Value for finding ICD-10 code in DB.
     * @var string
     */
    public string $query;

    /**
     * Found the ICD-10 code and description.
     * @var array
     */
    public array $results;

    public function render(): View
    {
        return view('livewire.encounter.encounter');
    }

    public function mount(int $patientId, ?int $encounterId = null): void
    {
        $this->patientId = $patientId;
        $this->encounterId = $encounterId;

        if ($this->encounterId) {
            $this->form->encounter = EncounterRepository::getEncounterData($this->patientId);
            $this->form->episode = EncounterRepository::getEpisodeData($this->encounterId);
            $this->form->conditions = EncounterRepository::getConditionData($this->encounterId);
            $this->form->conditions = $this->convertArrayKeysToCamelCase($this->form->conditions);
            $this->form->conditions = $this->formatConditions($this->form->conditions, $this->form->encounter['diagnoses']);
        } else {
            $this->setUuids();
            $this->setEmployeePartyData();
        }

        $this->setDefaultDate();
        $this->setPatientData();
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
        $approvedEpisodesData = PatientApi::getApprovedEpisodes($this->patientUuid, $buildSearchRequest);
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
        $conditionsData = PatientApi::getConditions($this->patientUuid, $buildSearchRequest);
    }

    /**
     * Search for ICD-10 in DB by the provided value.
     *
     * @param  string  $value
     * @return void
     */
    public function searchICD10(string $value): void
    {
        $this->query = $value;

        $this->results = DB::table('icd_10')
            ->where('code', 'ILIKE', "%$this->query%")
            ->orWhere('description', 'ILIKE', "%$this->query%")
            ->limit(50)
            ->get()
            ->toArray();
    }

    /**
     * Validate and save data.
     *
     * @return void
     * @throws ValidationException
     */
    public function save(): void
    {
        // update or create
        if ($this->encounterId) {
            $formEncounter = $this->updatePeriodDate($this->form->encounter);
            $createdEncounterId = EncounterRepository::storeEncounterRequest(
                $formEncounter,
                $this->form->episode,
                $this->patientId
            );
            EncounterRepository::storeCondition($this->form->conditions, $createdEncounterId);
        } else {
            $encounter = $this->formatEncounterRequest();
            $episode = $this->formatEpisodeRequest();
            $condition = $this->formatConditionRequest();
            $immunizations = $this->formatImmunizationsRequest();
            $createdEncounterId = EncounterRepository::storeEncounterRequest(
                $encounter['encounter'],
                $episode,
                $this->patientId
            );
            EncounterRepository::storeCondition($condition['conditions'], $createdEncounterId);
        }

        $encounter = PatientApi::getShortEncounterBySearchParams($this->patientUuid);
        $job = PatientApi::getJobsDetailsById('67e64af98c67240046bb4b2f');
    }

    /**
     * Submit encrypted data about person encounter.
     *
     * @return void
     * @throws ApiException|ValidationException
     */
    public function signPerson(): void
    {
        try {
            $this->form->rulesForModelValidate(['encounter', 'episode', 'conditions']);
        } catch (ValidationException $e) {
            $this->dispatch('flashMessage', [
                'message' => $e->validator->errors()->first(),
                'type' => 'error'
            ]);

            throw $e;
        }

        $this->createEpisode();

        // Note: No update operations are allowed. All IDs, submitted as PK, should be unique for eHealth.
        // TODO: додати перевірку на унікальність uuid, трішки потім. uuid має бути унікальний для пацієнта а не унікальним в цілому?
        $preRequestEncounter = $this->formatEncounterRequest();
        $preRequestCondition = $this->formatConditionRequest();

        $base64EncryptedData = $this->sendEncryptedData(
            array_merge(
                $preRequestEncounter,
                $preRequestCondition
            ),
            Auth::user()->tax_id
        );

        $prepareSubmitEncounter = [
            'visit' => (object) [
                'id' => $this->visitUuid,
                'period' => (object) [
                    'start' => $preRequestEncounter['encounter']['period']['start'],
                    'end' => $preRequestEncounter['encounter']['period']['end']
                ]
            ],
            'signed_data' => $base64EncryptedData
        ];

        $submitEncounter = PatientApi::submitEncounter($this->patientUuid, $prepareSubmitEncounter);
        dd($submitEncounter);
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
     * @return void
     */
    private function createEpisode(): void
    {
        try {
            PatientApi::createEpisode($this->patientUuid, $this->formatEpisodeRequest());
        } catch (ApiException) {
            $this->dispatch('flashMessage', [
                'message' => __('Виникла помилка при створенні епізоду. Зверніться до адміністратора.'),
                'type' => 'error'
            ]);
        }
    }

    /**
     * Set patient and related data.
     *
     * @return void
     */
    private function setPatientData(): void
    {
        $patient = Person::with(['encounters'])
            ->select(['id', 'uuid', 'first_name', 'last_name', 'second_name'])
            ->where('id', $this->patientId)
            ->first()
            ?->toArray();

        $this->patientUuid = $patient['uuid'];
        $this->firstName = $patient['first_name'];
        $this->lastName = $patient['last_name'];
        $this->secondName = $patient['second_name'] ?? null;
        $this->legalEntityType = Auth::user()->legalEntity->type;
        // TODO: брати із Auth, коли буде відповідна структура в БД
        $this->employeeType = Employee::find(1)->employeeType;
    }

    /**
     * Set required employee party data.
     *
     * @return void
     */
    protected function setEmployeePartyData(): void
    {
        // TODO: потім взяти employee авторизованого
        $employee = Employee::find(1);

        $this->form->encounter['performer']['identifier']['value'] = $employee?->uuid;
        $this->form->episode['careManager']['identifier']['value'] = $employee?->uuid;
    }

    /**
     * Get all user divisions, and set default if only one exists.
     *
     * @return void
     */
    protected function getDivisionData(): void
    {
        $this->divisions = Auth::user()->legalEntity->division->toArray();

        // set division if only one exist
        if (count($this->divisions) === 1) {
            $this->form->encounter['division']['identifier']['value'] = $this->divisions[0]['uuid'];
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

        // set default encounter class, if there is only one
        if (count($this->dictionaries['eHealth/encounter_classes']) === 1) {
            $this->form->encounter['class']['code'] = array_key_first($this->dictionaries['eHealth/encounter_classes']);
        }
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

    /**
     * Generate UUIDs.
     *
     * @return void
     */
    private function setUuids(): void
    {
        $this->encounterUuid = Str::uuid()->toString();
        $this->visitUuid = Str::uuid()->toString();
        $this->episodeUuid = Str::uuid()->toString();
    }

    /**
     * Set default encounter period date.
     *
     * @return void
     */
    private function setDefaultDate(): void
    {
        if ($this->encounterId) {
            $this->form->encounter['period']['date'] = CarbonImmutable::parse($this->form->encounter['period']['start'])->format('Y-m-d');
            $this->form->encounter['period']['start'] = CarbonImmutable::parse($this->form->encounter['period']['start'])->format('H:i');
            $this->form->encounter['period']['end'] = CarbonImmutable::parse($this->form->encounter['period']['end'])->format('H:i');
        } else {
            $now = CarbonImmutable::now();
            $this->form->encounter['period']['start'] = $now->format('H:i');
            $this->form->encounter['period']['end'] = $now->addMinutes(15)->format('H:i');
        }
    }

    /**
     * Validate and format encounter data requests.
     *
     * @return array
     */
    private function formatEncounterRequest(): array
    {
        $this->form->encounter['id'] = $this->encounterUuid;
        $this->form->encounter['visit']['identifier']['value'] = $this->visitUuid;
        $this->form->encounter['episode']['identifier']['value'] = $this->episodeUuid;

        // add system if priority is provided or when it's required
        if ($this->form->encounter['class']['code'] === 'INPATIENT' || $this->form->encounter['class']['code']) {
            $this->form->encounter['priority']['coding'][0]['system'] = 'eHealth/encounter_priority';
        }

        $this->form->encounter['diagnoses'] = array_map(function (array $diagnose) {
            // Create a unique UUID for each diagnosis, and use them in condition
            $diagnoseUuid = Str::uuid()->toString();
            $diagnose['diagnoses']['condition']['identifier']['value'] = $diagnoseUuid;
            $this->diagnoseUuids[] = $diagnoseUuid;

            // delete rank if not provided
            if ($diagnose['diagnoses']['rank'] === '') {
                unset($diagnose['diagnoses']['rank']);
            }

            return $diagnose['diagnoses'];
        }, $this->form->conditions);

        if ($this->form->encounter['division']['identifier']['value']) {
            $this->form->encounter['division']['identifier']['type']['coding'][0] = [
                'system' => 'eHealth/resources',
                'code' => 'division'
            ];
        }

        $encounterForm = $this->updatePeriodDate($this->form->encounter);

        return schemaService()
            ->setDataSchema(['encounter' => $encounterForm], app(PatientApi::class))
            ->requestSchemaNormalize()
            ->getNormalizedData();
    }

    /**
     * Validate and format condition data requests.
     *
     * @return array
     */
    private function formatConditionRequest(): array
    {
        $conditions = array_map(
            function (array $condition, int $index) {
                // set ID same as diagnose
                $condition['id'] = $this->diagnoseUuids[$index];

                $condition['context']['identifier']['type']['coding'][0] = [
                    'system' => 'eHealth/resources',
                    'code' => 'encounter'
                ];
                $condition['context']['identifier']['value'] = $this->encounterUuid;

                // unset if code not provided
                if ($condition['severity']['coding'][0]['code'] === '') {
                    unset($condition['severity']);
                }

                if ($condition['primarySource']) {
                    // TODO: потім взяти employee авторизованого
                    $employee = Employee::find(1);
                    $condition['asserter']['identifier']['value'] = $employee?->uuid;

                    unset($condition['reportOrigin']);
                } else {
                    unset($condition['asserter']);
                }

                // convert dates
                if (isset($condition['onsetTime'])) {
                    $condition['onsetDate'] = convertToISO8601($condition['onsetDate'] . $condition['onsetTime']);
                    $condition['assertedDate'] = convertToISO8601($condition['assertedDate'] . $condition['assertedTime']);
                    unset($condition['onsetTime'], $condition['assertedTime'], $condition['diagnoses']);
                }

                return $condition;
            },
            $this->form->conditions,
            array_keys($this->form->conditions)
        );

        return schemaService()
            ->setDataSchema(['conditions' => $conditions], app(PatientApi::class))
            ->requestSchemaNormalize()
            ->getNormalizedData();
    }

    /**
     * Formatting conditions for showing in frontend.
     *
     * @param  array  $conditions
     * @param  array  $diagnoses
     * @return array
     */
    protected function formatConditions(array $conditions, array $diagnoses): array
    {
        return collect($conditions)
            ->map(function (array $condition, int $index) use ($diagnoses) {
                // add diagnoses array to conditions
                if (isset($diagnoses[$index])) {
                    $condition['diagnoses'] = $diagnoses[$index];
                }

                $originalOnsetDate = $condition['onsetDate'];
                $originalAssertedDate = $condition['assertedDate'];

                // set date
                $condition['onsetDate'] = CarbonImmutable::parse($originalOnsetDate)->format('Y-m-d');
                $condition['onsetTime'] = CarbonImmutable::parse($originalOnsetDate)->format('H:i');
                $condition['assertedDate'] = CarbonImmutable::parse($originalAssertedDate)->format('Y-m-d');
                $condition['assertedTime'] = CarbonImmutable::parse($originalAssertedDate)->format('H:i');

                return $condition;
            })
            ->toArray();
    }

    /**
     * Validate and format episode data requests.
     *
     * @return array
     */
    protected function formatEpisodeRequest(): array
    {
        $this->form->episode['id'] = $this->episodeUuid;
        $this->form->episode['managingOrganization']['identifier']['value'] = Auth::user()->legalEntity->uuid;
        $this->form->episode['period']['start'] = convertToISO8601($this->form->encounter['period']['date'] . $this->form->encounter['period']['start']);

        return schemaService()
            ->setDataSchema($this->form->episode, app(PatientApi::class))
            ->requestSchemaNormalize('schemaEpisodeRequest')
            ->getNormalizedData();
    }

    /**
     * /**
     *  Validate and format immunizations data requests.
     *
     * @return array
     */
    protected function formatImmunizationsRequest(): array
    {
        $immunizations = array_map(static function ($immunization) {
            $immunization['id'] = Str::uuid()->toString();

            if ($immunization['primarySource']) {
                unset($immunization['reportOrigin']);

                // TODO: потім взяти employee авторизованого
                $employee = Employee::findOrFail(1);
                $immunization['performer']['identifier']['value'] = $employee->uuid;
            } else {
                unset($immunization['performer']);
            }

            if ($immunization['notGiven']) {
                unset($immunization['explanation']['reasons']);
            } else {
                unset($immunization['explanation']['reasonsNotGiven']);
            }

            $immunization['date'] = convertToISO8601($immunization['date'] . $immunization['time']);
            unset($immunization['time']);

            return $immunization;
        }, $this->form->immunizations);

        return schemaService()
            ->setDataSchema(['immunizations' => $immunizations], app(PatientApi::class))
            ->requestSchemaNormalize()
            ->getNormalizedData();
    }
}
