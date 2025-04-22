<?php

declare(strict_types=1);

namespace App\Livewire\Encounter\Forms;

use App\Models\Employee\Employee;
use App\Rules\Cyrillic;
use App\Rules\InDictionary;
use App\Rules\OnlyOnePrimaryDiagnosis;
use App\Rules\TimeInPast;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\ProhibitedIf;
use Illuminate\Validation\Rules\RequiredIf;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class Encounter extends Form
{
    #[Validate([
        'encounter.period.start' => ['required', 'date', 'before_or_equal:now'],
        'encounter.period.end' => ['required', 'date', 'after:encounter.period.start'],
        'encounter.class.code' => ['required', 'string', new InDictionary('eHealth/encounter_classes')],
        'encounter.type.coding.*.code' => ['required', 'string', new InDictionary('eHealth/encounter_types')],
        'encounter.priority' => ['required_if:encounter.class.code,INPATIENT', 'array'],
        'encounter.priority.coding.*.code' => ['required', 'string', new InDictionary('eHealth/encounter_priority')],
        'encounter.reasons' => ['required_if:encounter.class.code,PHC', 'array'],
        'encounter.reasons.*.coding.*.code' => ['required', 'string', new InDictionary('eHealth/ICPC2/reasons')],
        'encounter.reasons.*.text' => ['nullable', 'string', new Cyrillic()],
        'encounter.diagnoses' => ['required_unless:encounter.type.coding.0.code,intervention', new OnlyOnePrimaryDiagnosis(), 'array'],
        'encounter.diagnoses.*.role.coding.*.code' => ['required', 'string', new InDictionary('eHealth/diagnosis_roles')],
        'encounter.diagnoses.*.rank' => ['nullable', 'integer', 'min:1', 'max:10'],
        'encounter.actions' => [
            'required_if:encounter.class.code,PHC', 'prohibited_unless:encounter.class.code,PHC', 'array'
        ],
        'encounter.actions.*.coding.*.code' => ['required', 'string', new InDictionary('eHealth/ICPC2/actions')],
        'encounter.actions.*.text' => ['nullable', 'string', new Cyrillic()],
        'encounter.division.identifier.value' => ['required', 'uuid']
    ])]
    public array $encounter = [
        'status' => 'finished',
        'visit' => [
            'identifier' => [
                'type' => ['coding' => [['system' => 'eHealth/resources', 'code' => 'visit']]]
            ]
        ],
        'episode' => [
            'identifier' => [
                'type' => ['coding' => [['system' => 'eHealth/resources', 'code' => 'episode']]]
            ]
        ],
        'class' => [
            'system' => 'eHealth/encounter_classes'
        ],
        'type' => [
            'coding' => [['system' => 'eHealth/encounter_types']]
        ],
        'performer' => [
            'identifier' => [
                'type' => ['coding' => [['system' => 'eHealth/resources', 'code' => 'employee']]]
            ]
        ],
        'reasons' => [],
        'diagnoses' => [],
        'actions' => []
    ];

    #[Validate([
        'episode.type.code' => ['required', 'string', new InDictionary('eHealth/episode_types')],
        'episode.name' => ['required', 'string', new Cyrillic()],
        'episode.period.start' => ['required', 'date', 'before_or_equal:now']
    ])]
    public array $episode = [
        'type' => [
            'system' => 'eHealth/episode_types'
        ],
        'status' => 'active',
        'managingOrganization' => [
            'identifier' => [
                'type' => [
                    'coding' => [['system' => 'eHealth/resources', 'code' => 'legal_entity']]
                ]
            ]
        ],
        'careManager' => [
            'identifier' => [
                'type' => [
                    'coding' => [['system' => 'eHealth/resources', 'code' => 'employee']]
                ]
            ]
        ]
    ];

    #[Validate([
        'conditions' => ['required', 'array'],
        'conditions.*.primarySource' => ['required', 'boolean'],
        'conditions.*.asserter' => ['required_if:conditions.*.primarySource,true', 'array'],
        'conditions.*.reportOrigin' => ['required_if:conditions.*.primarySource,false', 'array'],
        'conditions.*.code.coding.0.code' => ['required', 'string'],
        'conditions.*.code.coding.1.code' => ['required_if:encounter.class.code,AMB, INPATIENT', 'string'],
        'conditions.*.clinicalStatus' => ['required', 'string'],
        'conditions.*.verificationStatus' => ['required', 'string'],
        'conditions.*.severity.coding.*.code' => [
            'nullable', 'string', new InDictionary('eHealth/condition_severities')
        ],
        'conditions.*.onsetDate' => ['required', 'before:tomorrow', 'date_format:Y-m-d'],
        'conditions.*.onsetTime' => ['required', 'date_format:H:i', new TimeInPast()],
        'conditions.*.assertedDate' => ['nullable', 'before:tomorrow', 'date_format:Y-m-d'],
        'conditions.*.assertedTime' => ['nullable', 'date_format:H:i', new TimeInPast()]
    ])]
    public array $conditions;

    public array $evidences;

    public array $immunizations;

    protected function rules(): array
    {
        return [
            'immunizations.*.primarySource' => ['required', 'boolean'],
            'immunizations.*.performer' => [
                'required_if:immunizations.*.primarySource,true', 'prohibited_if:immunizations.*.primarySource,false',
                'array'
            ],
            'immunizations.*.reportOrigin' => [
                'required_if:immunizations.*.primarySource,false', 'prohibited_if:immunizations.*.primarySource,true'
            ],
            'immunizations.*.reportOrigin.coding.*.code' => [
                'string', new InDictionary('eHealth/immunization_report_origins')
            ],
            'immunizations.*.notGiven' => ['declined_if:immunizations.*.primarySource,false', 'boolean'],
            'immunizations.*.explanation.reasonsNotGiven' => [
                $this->requiredIfPrimarySourceAndNotGivenTrue(), 'prohibited_if:immunizations.*.notGiven,false', 'array'
            ],
            'immunizations.*.explanation.reasonsNotGiven.coding.*.code' => [
                'required', 'string', new InDictionary('eHealth/reason_not_given_explanations')
            ],
            'immunizations.*.vaccineCode.coding.*.code' => [
                'required', 'string', new InDictionary('eHealth/vaccine_codes')
            ],
            'immunizations.*.explanation.reasons' => [
                Rule::unless(
                    fn () => collect($this->immunizations)->contains(
                        fn ($immunization) => $immunization['primarySource'] === false &&
                            $immunization['notGiven'] === true
                    ),
                    'required'
                ),
                $this->prohibitedIfPrimarySourceAndNotGivenTrue(), 'array'
            ],
            'immunizations.*.explanation.reasons.*.coding.*.code' => [
                'required', 'string', new InDictionary('eHealth/reason_explanations')
            ],
            'immunizations.*.manufacturer' => [$this->requiredIfPrimarySourceTrueAndNotGivenFalse(), 'string'],
            'immunizations.*.lotNumber' => [$this->requiredIfPrimarySourceTrueAndNotGivenFalse(), 'string'],
            'immunizations.*.expirationDate' => [$this->requiredIfPrimarySourceTrueAndNotGivenFalse(), 'string'],

            'immunizations.*.doseQuantity.value' => [
                $this->requiredIfPrimarySourceTrueAndNotGivenFalse(), $this->requiredIfPrimarySourceAndNotGivenFalse(),
                'integer'
            ],
            'immunizations.*.doseQuantity.unit' => [
                $this->requiredIfPrimarySourceTrueAndNotGivenFalse(), $this->requiredIfPrimarySourceAndNotGivenFalse(),
                'string'
            ],
            'immunizations.*.doseQuantity.code' => [
                $this->requiredIfPrimarySourceTrueAndNotGivenFalse(),
                new InDictionary('eHealth/immunization_dosage_units'), 'string'
            ],
            'immunizations.*.site' => [
                $this->requiredIfPrimarySourceTrueAndNotGivenFalse(), 'array'
            ],
            'immunizations.*.site.coding.*.code' => [
                'required', 'string', new InDictionary('eHealth/immunization_body_sites')
            ],
            'immunizations.*.route' => [$this->requiredIfPrimarySourceTrueAndNotGivenFalse(), 'array'],
            'immunizations.*.route.coding.*.code' => [
                'required', 'string', new InDictionary('eHealth/vaccination_routes')
            ],
            'immunizations.*.vaccinationProtocols.doseSequence' => ['required', 'integer'],
            'immunizations.*.vaccinationProtocols.authority' => ['required', 'array'],
            'immunizations.*.vaccinationProtocols.series' => ['required', 'string'],
            'immunizations.*.vaccinationProtocols.seriesDoses' => ['required', 'integer'],
            'immunizations.*.vaccinationProtocols.targetDiseases' => ['required', 'array'],
            'immunizations.*.date' => ['required', 'before:tomorrow', 'date']
        ];
    }

    /**
     * Validate provided models by corresponding rules.
     *
     * @param  array  $models
     * @return array
     * @throws ValidationException
     */
    public function rulesForModelValidate(array $models): array
    {
        $rules = [];

        foreach ($models as $model) {
            $rules += $this->rulesForModel($model)->toArray();
        }

        $this->addAllowedEpisodeCareManagerEmployeeTypes($rules);
        $this->addAllowedEncounterClasses($rules);
        $this->addAllowedEncounterTypes($rules);

        return $this->validate($rules);
    }

    /**
     * Validate form by name.
     *
     * @param  string  $formName
     * @param  array  $formData
     * @return void
     * @throws ValidationException
     */
    public function validateForm(string $formName, array $formData): void
    {
        $validator = Validator::make($formData, $this->getRulesForModel($formName));

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Get all the rules for the provided model name.
     *
     * @param  string  $model
     * @return array
     */
    public function getRulesForModel(string $model): array
    {
        return $this->rulesForModel($model)->toArray();
    }

    /**
     * Add allowed values for episode type code.
     *
     * @param  array  $rules
     * @return void
     */
    private function addAllowedEpisodeCareManagerEmployeeTypes(array &$rules): void
    {
        $allowedValues = $this->getAllowedValues(
            'ehealth.legal_entity_episode_types',
            'ehealth.employee_episode_types'
        );
        $this->addAllowedRule($rules, 'episode.type.code', $allowedValues);
    }

    /**
     * Add allowed values for encounter classes.
     *
     * @param  array  $rules
     * @return void
     */
    private function addAllowedEncounterClasses(array &$rules): void
    {
        $allowedValues = $this->getAllowedValues(
            'ehealth.legal_entity_encounter_classes',
            'ehealth.employee_encounter_classes'
        );
        $this->addAllowedRule($rules, 'encounter.class.code', $allowedValues);
    }

    /**
     * Add allowed values for encounter types.
     *
     * @param  array  $rules
     * @return void
     */
    private function addAllowedEncounterTypes(array &$rules): void
    {
        $allowedValues = config('ehealth.encounter_class_encounter_types')[key($this->component->dictionaries['eHealth/encounter_classes'])];
        $this->addAllowedRule($rules, 'encounter.type.coding.code', $allowedValues);
    }

    /**
     * Get allowed values by config keys.
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
                $additionalValues[Employee::find(1)->employee_type]
            );
        }

        return $allowedValues;
    }

    /**
     * Add 'in' rule by key and with allowed values.
     *
     * @param  array  $rules
     * @param  string  $ruleKey
     * @param  array  $allowedValues
     * @return void
     */
    private function addAllowedRule(array &$rules, string $ruleKey, array $allowedValues): void
    {
        $rules[$ruleKey][] = 'in:' . implode(',', $allowedValues);
    }

    /**
     * Add a rule that makes the field required if primarySource equals true and NotGiven equals false.
     *
     * @return RequiredIf
     */
    private function requiredIfPrimarySourceTrueAndNotGivenFalse(): RequiredIf
    {
        return Rule::requiredIf(
            fn () => collect($this->immunizations)->contains(
                fn ($immunization) => $immunization['primarySource'] === true && $immunization['notGiven'] === false
            )
        );
    }

    /**
     * Add a rule that makes the field required if primarySource and NotGiven equals true.
     *
     * @return RequiredIf
     */
    private function requiredIfPrimarySourceAndNotGivenTrue(): RequiredIf
    {
        return Rule::requiredIf(
            fn () => collect($this->immunizations)->contains(
                fn ($immunization) => $immunization['primarySource'] === true && $immunization['notGiven'] === true
            )
        );
    }

    /**
     * Add a rule that makes the field required if primarySource and NotGiven equals false.
     *
     * @return RequiredIf
     */
    private function requiredIfPrimarySourceAndNotGivenFalse(): RequiredIf
    {
        return Rule::requiredIf(
            fn () => collect($this->immunizations)->contains(
                fn ($immunization) => $immunization['primarySource'] === false && $immunization['notGiven'] === false
            )
        );
    }

    /**
     * Add a rule that makes the field required if primarySource and NotGiven equals false.
     *
     * @return ProhibitedIf
     */
    private function prohibitedIfPrimarySourceAndNotGivenTrue(): ProhibitedIf
    {
        return Rule::prohibitedIf(
            fn () => collect($this->immunizations)->contains(
                fn ($immunization) => $immunization['primarySource'] === true && $immunization['notGiven'] === true
            )
        );
    }
}
