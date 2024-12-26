<?php

namespace App\Livewire\Division\Forms;

use App\Rules\DivisionRules\DivisionStatusRule;
use App\Rules\DivisionRules\HealthcareRules\CategoryInPharmacyRule;
use App\Rules\DivisionRules\HealthcareRules\CategoryRule;
use App\Rules\DivisionRules\HealthcareRules\LicenseRule;
use App\Rules\DivisionRules\HealthcareRules\NotAvailableTimeRule;
use Livewire\Attributes\Validate;
use Livewire\Features\SupportFormObjects\Form;
use App\Exceptions\CustomValidationException;
use App\Rules\DivisionRules\HealthcareRules\AvailableTimeRule;
use App\Rules\DivisionRules\HealthcareRules\CategoryInDictionaryRule;
use App\Rules\DivisionRules\HealthcareRules\ProvidingConditionRule;
use App\Rules\DivisionRules\HealthcareRules\SpecialityTypeInDictionaryRule;
use App\Rules\DivisionRules\LegalEntityStatusRule;
use Exception;

class HealthCareFormRequest extends Form
{
    const HEALTHCARE_SERVICE_LEGAL_ENTITIES_ALLOWED_TYPE = 'MSP';
    const LEGAL_ENTITY_PRIMARY_CARE_PROVIDING_CONDITIONS = 'OUTPATIENT';

    const HEALTHCARE_SERVICE_FORM_CLEANUP = [
        'speciality_type',
        'comment',
        'available_time',
        'not_available'
    ];

    #[Validate([
        'healthcare_service.providing_condition' => 'required',
        'healthcare_service.speciality_type' => 'required',
//        'healthcare_service.type' => 'required_if:healthcare_service.category,PHARMACY_DRUGS',
//        'healthcare_service.license_id' => 'required_if:healthcare_service.category,PHARMACY_DRUGS',
    ])]
    public ?array $healthcare_service = [] ;

    /**
     * Property for custom rules (not used really).
     * Need for custom rules working.
     */
    public ?string $customRule;

    public ?string $comment = '';

    public function getHealthcareService(): array
    {
        return $this->healthcare_service;
    }

    public function setHelathcareService(array $hcs)
    {
        $this->healthcare_service = $hcs;
    }

    public function healthcareServiceClean(string $category = ''): void
    {
        $formCleanup = self::HEALTHCARE_SERVICE_FORM_CLEANUP;

        if (
            $category === self::HEALTHCARE_SERVICE_LEGAL_ENTITIES_ALLOWED_TYPE &&
            $this->healthcare_service['providing_condition'] == self::LEGAL_ENTITY_PRIMARY_CARE_PROVIDING_CONDITIONS
        ) {
            $this->healthcare_service = array_filter($this->healthcare_service,
            function($key) use($formCleanup) {
                return !in_array($key, $formCleanup);
            },
            ARRAY_FILTER_USE_KEY);
        } else {
            $this->healthcare_service = [];
        }
    }

    public function getHealthcareServiceParam(string $param): mixed
    {
        return $this->healthcare_service[$param] ?? '';
    }

    public function setHealthcareServiceParam(string $param, mixed $value): void
    {
        $this->healthcare_service[$param] = $value;
    }

    protected function customRulesValidation(string $mode): string
    {
        foreach ($this->customRules($mode) as $rule) {
            try {
                $rule->validate('', '', fn() => null);
            } catch (CustomValidationException $e) {
                return $e->getMessage();
            }
        }

        return '';
    }

    /**
     * Do form's validation (correctness of filling the form fields)
     *
     * @return mixed
     */
    public function doValidation(string $mode)
    {
        $this->validate();

        $failMessage = $this->customRulesValidation($mode);

        return $failMessage;
    }

    public function addAvailableTime($k): void
    {
         $this->healthcare_service['available_time'][$k] = [
            'days_of_week' => get_day_key($k),
            'all_day' => false,
            'available_start_time' =>'',
            'available_end_time' =>'',
        ];
    }

    public function removeAvailableTime($k): void
    {
        unset($this->healthcare_service['available_time'][$k]);
    }

    public function addNotAvailableTime(): void
    {
        $this->healthcare_service['not_available'][] = [
            'description' => '',
            'during' => [
                'start' => '',
                'end' => '',
            ],
        ];
    }

    public function removeNotAvailable($k): void
    {
        unset($this->healthcare_service['not_available'][$k]);
    }

    /**
     * TODO: add rule for next cases:
     *  - Check that division exists in PRM DB
     *  - Validate category for HEALTHCARE_SERVICE_<$.category>_LICENSE_TYPE
     *      - check that Healthcare service category must have linked license
     *      - check that License must not be submitted for healthcare service category
     *      - check that License type does not match healthcare service category
     *  - Check that providing condition in request is allowed for legal entity type according to 'Configurations for Healthcare services' ??
     */
    protected function customRules(string $mode)
    {
        $division = $this->component->division;

        $validationRules = [];

        $commonValidationRules = [
            // Check that legal entity is in ‘ACTIVE’ or ‘SUSPENDED’ status
            new LegalEntityStatusRule(),
            // Check that division status = ‘ACTIVE’
            new DivisionStatusRule($division)
        ];

        $timeValidationRules = [
            // Check that end time should be greater then start
            new AvailableTimeRule($division, $this->healthcare_service),
            // Check that end time should be greater then start
            new NotAvailableTimeRule($division, $this->healthcare_service)
        ];

        $storeValidationRules = [
            // Check that category is a value from HEALTHCARE_SERVICE_CATEGORIES dictionary
            new CategoryInDictionaryRule($division, $this->healthcare_service),
            // Check that speciality type is a value from SPECIALITY_TYPE dictionary
            new SpecialityTypeInDictionaryRule($division, $this->healthcare_service),
            // Check that there is no another record with the same healthcare service, division_id and category = ‘PHARMACY’
            new CategoryInPharmacyRule($division),
            // Check that there is any valid license for the healthcare service's category
            new LicenseRule($division, $this->healthcare_service),
            // Check that there is no another record with the same healthcare service, division_id, category and type
            new CategoryRule($division, $this->healthcare_service),
            // Check that there is no another record with the same healthcare service, division_id, speciality type and providing condition
            new ProvidingConditionRule($division, $this->healthcare_service),
        ];

        if ($mode === 'edit') {
            $validationRules = array_merge($validationRules, $commonValidationRules, $timeValidationRules);
        } else {
            $validationRules = array_merge($validationRules, $commonValidationRules, $storeValidationRules, $timeValidationRules);
        }

        return $validationRules;
    }

    public function rules()
    {
        return [
            'healthcare_service.category' => 'required|max:255',
            'healthcare_service.comment' => 'sometimes|string|nullable',
         ];
    }
}
