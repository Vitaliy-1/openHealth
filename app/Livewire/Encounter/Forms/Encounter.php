<?php

declare(strict_types=1);

namespace App\Livewire\Encounter\Forms;

use App\Models\Employee\Employee;
use App\Rules\Cyrillic;
use App\Rules\InDictionary;
use App\Rules\TimeInPast;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class Encounter extends Form
{
    #[Validate([
        'encounter.period.date' => ['required', 'before:tomorrow', 'date_format:Y-m-d'],
        'encounter.period.start' => ['required', 'date_format:H:i', new TimeInPast()],
        'encounter.period.end' => ['required', 'date_format:H:i', 'after:encounter.period.start', new TimeInPast()],
        'encounter.class.code' => ['required', 'string', new InDictionary('eHealth/encounter_classes')],
        'encounter.type.coding.*.code' => ['required', 'string', new InDictionary('eHealth/encounter_types')],
        'encounter.priority' => ['required_if:encounter.class.code,INPATIENT', 'array'],
        'encounter.priority.coding.*.code' => ['required', 'string', new InDictionary('eHealth/encounter_priority')],
        'encounter.reasons' => ['required_if:encounter.class.code,PHC', 'array'],
        'encounter.reasons.*.coding.*.code' => ['required', 'string', new InDictionary('eHealth/ICPC2/reasons')],
        'encounter.reasons.*.text' => ['nullable', 'string', new Cyrillic()],
        // TODO: Encounter must have exactly one primary diagnosis
        'encounter.diagnoses' => ['required_unless:encounter.type.coding.0.code,intervention', 'array'],
        'encounter.diagnoses.role.coding.*.code' => ['required', 'string', new InDictionary('eHealth/diagnosis_roles')],
        'encounter.diagnoses.rank' => ['nullable', 'integer', 'min:1', 'max:10'],
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
        'episode' => ['required', 'array'],
        'episode.type.code' => ['required', 'string', new InDictionary('eHealth/episode_types')],
        'episode.name' => ['required', 'string', new Cyrillic()]
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
        'conditions.*.severity.coding.*.code' => ['nullable', 'string', new InDictionary('eHealth/condition_severities')],
        'conditions.*.onsetDate' => ['required', 'before:tomorrow', 'date_format:Y-m-d'],
        'conditions.*.onsetTime' => ['required', 'date_format:H:i', new TimeInPast()],
        'conditions.*.assertedDate' => ['nullable', 'before:tomorrow', 'date_format:Y-m-d'],
        'conditions.*.assertedTime' => ['nullable', 'date_format:H:i', new TimeInPast()]
    ])]
    public array $conditions;

    public array $evidences;

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
}
