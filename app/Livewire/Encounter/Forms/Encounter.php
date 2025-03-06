<?php

declare(strict_types=1);

namespace App\Livewire\Encounter\Forms;

use App\Models\Employee\Employee;
use App\Rules\Cyrillic;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class Encounter extends Form
{
    #[Validate([
        'encounter.division.identifier.value' => ['nullable', 'string'],
        'encounter.period.date' => ['required', 'before:tomorrow', 'date_format:Y-m-d'],
        'encounter.period.start' => ['required', 'date_format:H:i'],
        'encounter.period.end' => ['required', 'date_format:H:i', 'after:encounter.period.start'],
        'encounter.class.code' => ['required', 'string'],
        'encounter.type.coding.code' => ['required', 'string'],
        'encounter.priority.coding.code' => ['required_if:encounter.class.code,INPATIENT', 'string'],
        'encounter.diagnoses.role.coding.code' => ['required', 'string'],
        'encounter.diagnoses.rank' => ['nullable', 'integer', 'min:1', 'max:10'],
        'encounter.episode.identifier.type.coding.code' => ['nullable', 'string'],
    ])]
    public array $encounter = [
        'status' => 'finished',
        'visit' => [
            'identifier' => [
                'type' => [
                    'coding' => [['system' => 'eHealth/resources', 'code' => 'visit']]
                ]
            ]
        ],
        'episode' => [
            'identifier' => [
                'type' => [
                    'coding' => [['system' => 'eHealth/resources', 'code' => 'episode']]
                ]
            ]
        ],
        'class' => [
            'system' => 'eHealth/encounter_classes'
        ],
        'type' => [
            'coding' => [['system' => 'eHealth/encounter_types']]
        ],
        'priority' => [
            'coding' => [['system' => 'eHealth/encounter_priority']]
        ],
        'performer' => [
            'identifier' => [
                'type' => [
                    'coding' => [['system' => 'eHealth/resources', 'code' => 'employee']]
                ]
            ]
        ],
        'division' => [
            'identifier' => [
                'type' => [
                    'coding' => [['system' => 'eHealth/resources', 'code' => 'division']]
                ]
            ]
        ],
        'diagnoses.condition.identifier.type.coding.system' => 'eHealth/resources',
        'diagnoses.condition.identifier.type.coding.code' => 'condition',
        'diagnoses.role.coding.system' => 'eHealth/diagnosis_roles',
        'encounter.reasons.coding.system' => 'eHealth/ICPC2/reasons',
    ];

    #[Validate([
        'episode.name' => ['required', 'string', new Cyrillic()],
        'episode.type.code' => ['required']
    ])]
    public array $episode = [
        'type' => [
            'system' => 'eHealth/episode_types'
        ],
        'status' => 'active',
        'managing_organization' => [
            'identifier' => [
                'type' => [
                    'coding' => [['system' => 'eHealth/resources', 'code' => 'legal_entity']]
                ]
            ]
        ],
        'care_manager' => [
            'identifier' => [
                'type' => [
                    'coding' => [['system' => 'eHealth/resources', 'code' => 'employee']]
                ]
            ]
        ]
    ];

    #[Validate([
        'condition.coding.code' => ['required', 'string'],
    ])]
    public array $condition = [
        'code.coding.system' => 'eHealth/ICD10/condition_codes',
    ];

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
            $allowedValues = array_intersect($allowedValues[Auth::user()->legalEntity->type],
                $additionalValues[Employee::find(1)->employee_type]);
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
