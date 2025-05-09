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
use App\Repositories\MedicalEvents\Repository;
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
        'eHealth/immunization_report_origins',
        'eHealth/immunization_statuses',
        'eHealth/vaccine_codes',
        'eHealth/immunization_dosage_units',
        'eHealth/vaccination_routes',
        'eHealth/immunization_body_sites',
        'eHealth/vaccination_authorities',
        'eHealth/vaccination_target_diseases'
    ];

    public string $visitUuid;
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
            $this->form->encounter = Repository::encounter()->get($this->encounterId);
            $this->form->episode = Repository::episode()->get($this->encounterId);

            $this->form->conditions = Repository::condition()->get($this->encounterId);
            $this->form->conditions = Repository::encounter()->formatConditions($this->form->conditions, $this->form->encounter['diagnoses']);

            $this->form->immunizations = Repository::immunization()->get($this->encounterId);
            $this->form->immunizations = Repository::immunization()->formatForView($this->form->immunizations);
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
     */
    public function save(): void
    {
        // update or create
        if ($this->encounterId) {
            $formattedEncounter = Repository::encounter()->formatPeriod($this->form->encounter);

            // Validate formatted data
            try {
                $this->form->validateForm('encounter', $formattedEncounter);
                $this->form->validateForm('episode', $this->form->episode);
                $this->form->validateForm('conditions', $this->form->conditions);
                $this->form->validateForm('immunizations', $this->form->immunizations);
            } catch (ValidationException $e) {
                $this->dispatch('flashMessage', [
                    'message' => $e->validator->errors()->first(),
                    'type' => 'error'
                ]);

                return;
            }

            $createdEncounterId = Repository::encounter()->store(
                $formattedEncounter,
                $this->form->episode,
                $this->patientId
            );
            Repository::condition()->store($this->form->conditions, $createdEncounterId);
        } else {
            $encounterRepository = Repository::encounter();

            $formattedEncounter = $encounterRepository->formatEncounterRequest($this->form->encounter, $this->form->conditions);
            $formattedEpisode = $encounterRepository->formatEpisodeRequest($this->form->episode, $this->form->encounter['period']);
            $formattedConditions = $encounterRepository->formatConditionsRequest($this->form->conditions);

            if (!empty($this->form->immunizations)) {
                $formattedImmunizations = $encounterRepository->formatImmunizationsRequest($this->form->immunizations);
            }

            // Validate formatted data
            try {
                $this->form->validateForm('encounter', $formattedEncounter);
                $this->form->validateForm('episode', $formattedEpisode);
                foreach ($formattedConditions['conditions'] as $index => $formattedCondition) {
                    $this->form->validateForm("conditions.$index", $formattedCondition);
                }

                if (isset($formattedImmunizations)) {
                    foreach ($formattedImmunizations['immunizations'] as $index => $formattedImmunization) {
                        $this->form->validateForm("immunizations.$index", $formattedImmunization);
                    }
                }
            } catch (ValidationException $e) {
                $this->dispatch('flashMessage', [
                    'message' => $e->validator->errors()->first(),
                    'type' => 'error'
                ]);

                return;
            }

            $createdEncounterId = Repository::encounter()->store(
                $formattedEncounter['encounter'],
                $formattedEpisode['episode'],
                $this->patientId
            );

            Repository::condition()->store($formattedConditions['conditions'], $createdEncounterId);

            if (isset($formattedImmunizations)) {
                Repository::immunization()->store($formattedImmunizations['immunizations'], $createdEncounterId);
            }
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
        $preRequestEncounter = Repository::encounter()->formatEncounterRequest($this->form->encounter, $this->form->conditions);
        $preRequestCondition = Repository::encounter()->formatConditionsRequest($this->form->conditions);

        $base64EncryptedData = $this->sendEncryptedData(
            array_merge(
                $preRequestEncounter,
                $preRequestCondition
            ),
            Auth::user()->tax_id
        );

        $prepareSubmitEncounter = [
            'visit' => (object)[
                'id' => $this->visitUuid,
                'period' => (object)[
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
            PatientApi::createEpisode($this->patientUuid, Repository::encounter()->formatEpisodeRequest($this->form->episode, $this->form->encounter['period']));
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
        $this->visitUuid = Str::uuid()->toString();
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
}
