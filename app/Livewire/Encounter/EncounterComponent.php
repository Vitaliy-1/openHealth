<?php

declare(strict_types=1);

namespace App\Livewire\Encounter;

use App\Classes\Cipher\Exceptions\ApiException;
use App\Classes\Cipher\Traits\Cipher;
use App\Classes\eHealth\Api\PatientApi;
use App\Classes\eHealth\Api\ServiceRequestApi;
use App\Livewire\Encounter\Forms\Api\EncounterRequestApi;
use App\Models\Employee\Employee;
use App\Models\Person\Person;
use App\Traits\FormTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Locked;
use Livewire\Component;
use App\Livewire\Encounter\Forms\Encounter as EncounterForm;
use Livewire\WithFileUploads;

class EncounterComponent extends Component
{
    use FormTrait;
    use Cipher;
    use WithFileUploads;

    public EncounterForm $form;

    /**
     * ID of the patient for which create an encounter.
     * @var int
     */
    #[Locked]
    public int $patientId;

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
     * List of authorized user's divisions.
     * @var array
     */
    public array $divisions;

    /**
     * KEP key.
     * @var object|null
     */
    public ?object $file = null;

    /**
     * Patient UUID for API requests.
     * @var string
     */
    protected string $patientUuid;

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

    /**
     * Search for referral number.
     *
     * @return void
     * @throws \App\Classes\eHealth\Exceptions\ApiException
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
     * @throws \App\Classes\eHealth\Exceptions\ApiException
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
     * @throws \App\Classes\eHealth\Exceptions\ApiException
     */
    public function searchForConditions(): void
    {
        $buildSearchRequest = EncounterRequestApi::buildGetConditions();
        $conditionsData = PatientApi::getConditions($this->patientUuid, $buildSearchRequest);
    }

    /**
     * Set patient and related data.
     *
     * @return void
     */
    protected function setPatientData(): void
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
     * Adjust episode types according to legal entity type and employee type.
     *
     * @return void
     */
    protected function adjustEpisodeTypes(): void
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
    protected function adjustEncounterClasses(): void
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
    protected function adjustEncounterTypes(): void
    {
        $allowedValues = config('ehealth.encounter_class_encounter_types')[key($this->dictionaries['eHealth/encounter_classes'])];
        $this->adjustDictionary('eHealth/encounter_types', $allowedValues);
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
     * @throws ApiException
     */
    protected function setCertificateAuthority(): array
    {
        return $this->getCertificateAuthority = $this->getCertificateAuthority();
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

    public function updatedFile(): void
    {
        $this->keyContainerUpload = $this->file;
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
                $allowedValues[Auth::user()->legalEntity->type],
                $additionalValues[Employee::find(1)->employeeType]
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
