<?php

declare(strict_types=1);

namespace App\Livewire\Encounter;

use App\Classes\Cipher\Exceptions\ApiException as CipherApiException;
use App\Classes\eHealth\Api\RuleEngineRulesApi;
use App\Classes\eHealth\Exceptions\ApiException as eHealthApiException;
use App\Classes\Cipher\Traits\Cipher;
use App\Classes\eHealth\Api\PatientApi;
use App\Classes\eHealth\Api\ServiceRequestApi;
use App\Livewire\Encounter\Forms\Api\EncounterRequestApi;
use App\Models\Employee\Employee;
use App\Models\Person\Person;
use App\Traits\FormTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Locked;
use Livewire\Component;
use App\Livewire\Encounter\Forms\EncounterForm as Form;
use Livewire\WithFileUploads;
use RuntimeException;

class EncounterComponent extends Component
{
    use FormTrait;
    use Cipher;
    use WithFileUploads;

    public Form $form;

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
     * List of existing patient episodes.
     * @var array
     */
    public array $episodes = [];

    /**
     * List of existing patient clinical impressions.
     * @var array
     */
    public array $clinicalImpressions = [];

    /**
     * List of existing patient encounters.
     * @var array
     */
    public array $encounters = [];

    /**
     * List of existing patient procedures.
     * @var array
     */
    public array $procedures = [];

    /**
     * List of existing patient diagnostic reports.
     * @var array
     */
    public array $diagnosticReports = [];

    /**
     * Episode type, new or existing.
     * @var string
     */
    public string $episodeType = 'new';

    /**
     * Full name of employee.
     * @var string
     */
    public string $employeeFullName;

    /**
     * KEP key.
     * @var object|null
     */
    public ?object $file = null;

    /**
     * Patient UUID for API requests.
     * @var string
     */
    public string $patientUuid;

    /**
     * Legal entity type of auth user.
     * @var string
     */
    protected string $legalEntityType;

    /**
     * Role of auth user.
     * @var string
     */
    protected string $role;

    /**
     * Data about auth employee.
     * @var Employee
     */
    protected Employee $authEmployee;

    /**
     * Found the ICD-10 code and description.
     * @var array
     */
    public array $results;

    /**
     * List of observation codes for categories.
     * @var array
     */
    public array $observationCodeMap;

    /**
     * List of observation values and type of data for specific categories.
     * @var array
     */
    public array $observationValueMap;

    /**
     * List of values for codeable concept.
     * @var array
     */
    public array $codeableConceptValues;

    /**
     * List of founded conditions and observations.
     * @var array
     */
    public array $conditionsAndObservations = [];

    /**
     * List of founded complication details for current episode.
     * @var array
     */
    public array $complicationDetails;

    /**
     * List of founded problems for current episode.
     * @var array
     */
    public array $problems;

    /**
     * List of dictionary names.
     * @var array|string[]
     */
    protected array $dictionaryNames = [
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
        'eHealth/vaccination_target_diseases',
        'eHealth/observation_categories',
        'eHealth/ICF/observation_categories',
        'eHealth/LOINC/observation_codes',
        'eHealth/stature',
        'eHealth/eye_colour',
        'eHealth/hair_color',
        'eHealth/hair_length',
        'GENDER',
        'eHealth/rankin_scale',
        'eHealth/LOINC/LL2009-0',
        'eHealth/LOINC/LL2021-5',
        'eHealth/occupation_type',
        'eHealth/vaccination_covid_groups',
        'eHealth/LOINC/LL2451-4',
        'eHealth/LOINC/LL360-9',
        'eHealth/ICF/qualifiers',
        'eHealth/ICF/qualifiers/extent_or_magnitude_of_impairment',
        'eHealth/ICF/qualifiers/nature_of_change_in_body_structure',
        'eHealth/ICF/qualifiers/anatomical_localization',
        'eHealth/ICF/qualifiers/performance',
        'eHealth/ICF/qualifiers/capacity',
        'eHealth/ICF/qualifiers/barrier_or_facilitator',
        'eHealth/observation_methods',
        'eHealth/observation_interpretations',
        'eHealth/body_sites',
        'eHealth/ucum/units',
        'eHealth/diagnostic_report_categories',
        'eHealth/procedure_categories',
        'eHealth/procedure_outcomes',
        'eHealth/clinical_impression_patient_categories'
    ];

    /**
     * Search for referral number.
     *
     * @return void
     * @throws eHealthApiException
     */
    public function searchForReferralNumber(): void
    {
        $buildSearchRequest = EncounterRequestApi::buildGetServiceRequestList($this->form->referralNumber);
        ServiceRequestApi::searchForServiceRequestsByParams($buildSearchRequest);
    }

    /**
     * Search for ICD-10 in DB by the provided value.
     *
     * @param  string  $value
     * @return void
     */
    public function searchICD10(string $value): void
    {
        $this->results = DB::table('icd_10')
            ->select(['code', 'description'])
            ->where('code', 'ILIKE', "%$value%")
            ->orWhere('description', 'ILIKE', "%$value%")
            ->limit(50)
            ->get()
            ->toArray();
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
     * Initialize the component data based on the patient ID.
     *
     * @param  int  $patientId
     * @return void
     */
    protected function initializeComponent(int $patientId): void
    {
        $authUser = Auth::user();

        if (!$authUser) {
            throw new RuntimeException('Authenticated user not found');
        }

        $this->patientId = $patientId;
        $this->legalEntityType = legalEntity()->type;
        $this->role = $authUser->roles->first()->name;
        $this->divisions = legalEntity()->division->toArray();

        $this->authEmployee = $authUser->getEncounterWriterEmployee();
        $this->employeeFullName = $this->authEmployee->fullName;

        $this->observationCodeMap = config('ehealth.observation_category_codes');
        $this->observationValueMap = config('ehealth.observation_code_values');

        $this->getDictionary();

        try {
            $this->dictionaries['eHealth/ICF/classifiers'] = dictionary()
                ->getLargeDictionary('eHealth/ICF/classifiers', false)
                ->getFlattenedChildValues();
            $this->dictionaries['eHealth/assistive_products'] = dictionary()
                ->getLargeDictionary('eHealth/assistive_products', false)
                ->getFlattenedChildValues();
        } catch (eHealthApiException) {
            $this->flashGeneralError();
        }

        $this->dictionaries['custom/services'] = dictionary()->getServiceDictionary();
        $this->loadRuleEngineRules();

        $this->codeableConceptValues = collect(config('ehealth.observation_code_values'))
            ->filter(static fn (array $value) => $value[1] === 'valueCodeableConcept')
            ->mapWithKeys(fn (array $value) => [
                $value[0] => $this->dictionaries[$value[0]] ?? [],
            ])
            ->toArray();

        $this->adjustEpisodeTypes();
        $this->adjustEncounterClasses();
        $this->adjustEncounterTypes();

        $this->setPatientData();
        $this->getDivisionData();
        $this->getEpisodes();

        try {
            $this->setCertificateAuthority();
        } catch (CipherApiException) {
            $this->flashGeneralError();
        }
    }

    /**
     * Search for procedure reasons in conditions and observations.
     *
     * @param  string  $episodeId
     * @return void
     */
    public function searchConditionsAndObservations(string $episodeId): void
    {
        // Validate that an episode ID is provided
        if (empty($episodeId)) {
            $this->addError('episode', 'Please select an episode first.');

            return;
        }

        $buildGetConditions = EncounterRequestApi::buildGetConditionsInEpisodeContext($this->patientUuid, $episodeId);
        $buildGetObservations = EncounterRequestApi::buildGetObservationsInEpisodeContext(
            $this->patientUuid,
            $episodeId
        );

        try {
            $conditions = collect(PatientApi::getConditionsInEpisodeContext(
                $this->patientUuid,
                $episodeId,
                $buildGetConditions
            )['data'])->map(static function (array $item) {
                return array_merge($item, ['type' => 'condition']);
            });

            $observations = collect(PatientApi::getObservationsInEpisodeContext(
                $this->patientUuid,
                $episodeId,
                $buildGetObservations
            )['data'])->map(static function (array $item) {
                return array_merge($item, ['type' => 'observation']);
            });

            $this->conditionsAndObservations = $conditions->merge($observations)->values()->all();
        } catch (eHealthApiException) {
            Log::channel('e_health_errors')
                ->error('Error while searching for conditions and observations in Encounter Component');

            $this->flashGeneralError();
        }
    }

    /**
     * Search for clinical impressions in episodes.
     *
     * @param  string  $episodeId
     * @return void
     */
    public function searchClinicalImpressions(string $episodeId): void
    {
        // Validate that an episode ID is provided
        if (empty($episodeId)) {
            $this->addError('episode', 'Please select an episode first.');

            return;
        }

        try {
            $params = EncounterRequestApi::buildGetClinicalImpressionBySearchParams(
                $this->patientUuid,
                episodeUuid: $episodeId
            );
            $this->clinicalImpressions = PatientApi::getClinicalImpressionBySearchParams(
                $this->patientUuid,
                $params
            )['data'];
        } catch (eHealthApiException) {
            Log::channel('e_health_errors')
                ->error('Error while searching for clinical impressions in Encounter Component');

            $this->flashGeneralError();
        }
    }

    /**
     * Search for complication details in conditions for selected episode.
     *
     * @return void
     */
    public function searchComplicationDetails(): void
    {
        $episodeId = $this->form->encounter['episode']['identifier']['value'] ?? null;

        // If the episode is not selected, don't perform a search.
        if (!isset($episodeId)) {
            return;
        }

        $buildGetConditions = EncounterRequestApi::buildGetConditionsInEpisodeContext($this->patientUuid, $episodeId);

        try {
            $this->complicationDetails = PatientApi::getConditionsInEpisodeContext(
                $this->patientUuid,
                $episodeId,
                $buildGetConditions
            )['data'];
        } catch (eHealthApiException) {
            Log::channel('e_health_errors')
                ->error('Error while searching for complication details in Encounter Component');

            $this->flashGeneralError();
        }
    }

    /**
     * Search for complication details in conditions for selected episode.
     *
     * @param  string  $episodeId
     * @return void
     */
    public function searchProblems(string $episodeId): void
    {
        // If the episode is not selected, don't perform a search.
        if (!isset($episodeId)) {
            return;
        }

        $buildGetConditions = EncounterRequestApi::buildGetConditionsInEpisodeContext($this->patientUuid, $episodeId);

        try {
            $this->problems = PatientApi::getConditionsInEpisodeContext(
                $this->patientUuid,
                $episodeId,
                $buildGetConditions
            )['data'];
        } catch (eHealthApiException) {
            Log::channel('e_health_errors')
                ->error('Error while searching for problems in Encounter Component');

            $this->flashGeneralError();
        }
    }

    /**
     * Search for complication details in conditions for selected episode.
     *
     * @param  string  $type  Example: encounter, procedure, diagnosticReport.
     * @param  string  $episodeId
     * @return void
     */
    public function searchSupportingInfo(string $type, string $episodeId): void
    {
        // If the episode is not selected, don't perform a search.
        if (!isset($episodeId)) {
            return;
        }

        try {
            switch ($type) {
                case 'encounter':
                    $params = EncounterRequestApi::buildGetEncountersBySearchParams(
                        episodeUuid: $episodeId,
                        managingOrganizationUuid: legalEntity()->uuid
                    );

                    $this->encounters = PatientApi::getEncountersBySearchParams(
                        $this->patientUuid,
                        $params
                    )['data'];
                    break;

                case 'procedure':
                    $params = EncounterRequestApi::buildGetProceduresBySearchParams(
                        $this->patientUuid,
                        episodeUuid: $episodeId,
                        managingOrganizationUuid: legalEntity()->uuid
                    );
                    $this->procedures = PatientApi::getProceduresBySearchParams(
                        $this->patientUuid,
                        $params
                    )['data'];
                    break;

                case 'diagnosticReport':
                    $params = EncounterRequestApi::buildGetDiagnosticReportsBySearchParams(
                        originEpisodeUuid: $episodeId,
                        managingOrganizationUuid: legalEntity()->uuid
                    );
                    $this->diagnosticReports = PatientApi::getDiagnosticReportsBySearchParams(
                        $this->patientUuid,
                        $params
                    )['data'];
                    break;

                default:
                    break;
            }
        } catch (eHealthApiException $e) {
            Log::channel('e_health_errors')
                ->error("Error while searching for $type in Encounter Component", [
                    'exception' => $e->getMessage(),
                    'type' => $type,
                    'episodeId' => $episodeId
                ]);

            $this->flashGeneralError();
        }
    }

    /**
     * Set patient and related data.
     *
     * @return void
     */
    protected function setPatientData(): void
    {
        $patient = Person::select(['uuid', 'first_name', 'last_name', 'second_name'])
            ->where('id', $this->patientId)
            ->firstOrFail()
            ->toArray();

        $this->patientUuid = $patient['uuid'];
        $this->firstName = $patient['first_name'];
        $this->lastName = $patient['last_name'];
        $this->secondName = $patient['second_name'] ?? null;
    }

    /**
     * Adjust episode types according to legal entity type and employee type.
     *
     * @return void
     */
    protected function adjustEpisodeTypes(): void
    {
        $keys = $this->getFilteredKeysFromConfig(
            "legal_entity_episode_types.$this->legalEntityType",
            "employee_episode_types.$this->role"
        );

        $this->adjustDictionary('eHealth/episode_types', $keys);
    }

    /**
     * Show encounter classes based on legal entity and employee type.
     *
     * @return void
     */
    protected function adjustEncounterClasses(): void
    {
        $keys = $this->getFilteredKeysFromConfig(
            "legal_entity_encounter_classes.$this->legalEntityType",
            "employee_encounter_classes.$this->role"
        );

        $this->adjustDictionary('eHealth/encounter_classes', $keys);

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
        $selectedClass = key($this->dictionaries['eHealth/encounter_classes']);
        $keys = $this->getFilteredKeysFromConfig("encounter_class_encounter_types.$selectedClass");

        $this->adjustDictionary('eHealth/encounter_types', $keys);
    }

    /**
     * Get all user divisions, and set default if only one exists.
     *
     * @return void
     */
    protected function getDivisionData(): void
    {
        // set division if only one exist
        if (count($this->divisions) === 1) {
            $this->form->encounter['division']['identifier']['value'] = $this->divisions[0]['uuid'];
        }
    }

    /**
     * Get all episodes for current patient.
     *
     * @return void
     */
    protected function getEpisodes(): void
    {
        try {
            $params = EncounterRequestApi::buildGetEpisodeBySearchParams(managingOrganizationId: legalEntity()->uuid);
            $this->episodes = PatientApi::getEpisodeBySearchParams($this->patientUuid, $params)['data'];
        } catch (eHealthApiException) {
            $this->flashGeneralError();
        }
    }

    /**
     * Get Certificate Authority from API.
     *
     * @return array
     * @throws CipherApiException
     */
    protected function setCertificateAuthority(): array
    {
        return $this->getCertificateAuthority = $this->getCertificateAuthority();
    }

    /**
     * Load rules from the API and save them into the cache.
     *
     * @return void
     */
    protected function loadRuleEngineRules(): void
    {
        $this->dictionaries['custom/rule_engine_rule_list'] = Cache::remember(
            'rule_engine_rule_list',
            now()->addDays(7),
            static fn () => RuleEngineRulesApi::getRuleEngineRuleList()['data']
        );

        foreach ($this->dictionaries['custom/rule_engine_rule_list'] as $dictionary) {
            $cacheKey = "rule_engine_details_{$dictionary['code']['code']}";

            $details = Cache::remember(
                $cacheKey,
                now()->addDays(7),
                static fn () => RuleEngineRulesApi::getRuleEngineRuleDetails($dictionary['id'])
            );

            $this->dictionaries['custom/rule_engine_details'][$details['code']['code']] = $details;
        }
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
