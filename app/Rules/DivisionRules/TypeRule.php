<?php

namespace App\Rules\DivisionRules;

use Illuminate\Contracts\Validation\ValidationRule;
use App\Exceptions\CustomValidationException;
use Closure;

class TypeRule implements ValidationRule
{
    const ALLOWED_LEGAL_ENTITY_TYPES = ['PRIMARY_CARE', 'MSP', 'MSP_PHARMACY'];

    const ALLOWED_DIVISION_TYPES = ['CLINIC', 'AMBULANT_CLINIC', 'FAP'];

    const DIVISION_TYPE_RULES_LIST = [
        'checkDivisionType',
        'checkMapping'
    ];

    protected string $message;

    protected array $dictionaries;

    protected array $division;

    public function __construct(array $division)
    {
        $this->division = $division;

        $this->message = __('validation.attributes.healthcareService.error.division.commonError');
    }

    /**
     * Division main rules validation
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        foreach (self::DIVISION_TYPE_RULES_LIST as $check) {
            if (!$this->$check()) {
                $this->throwError();
            }
        }
    }

    protected function throwError(): void
    {
        throw new CustomValidationException($this->message(), 'custom');
    }

    protected function setMessage(string $message): void
    {
        $this->message = $message;
    }

    protected function message(): string
    {
        return $this->message;
    }

    /**
     * Check that type exists in dictionaries
     *
     * @return bool
     */
    protected function checkDivisionType(): bool
    {
        $divisionType= $this->division['type'];
        $dictionary = dictionary()->getDictionary('DIVISION_TYPE');

        if (!in_array($divisionType, array_keys($dictionary))) {
            $this->setMessage(__('validation.attributes.healthcareService.error.division.type'));

            return false;
        }

        return true;
    }

    /**
     * Check mapping of legal_entity_type and division type
     *
     * @return bool
     */
    protected function checkMapping(): bool
    {
        $legalEntityType =auth()->user()->legalEntity->type;
        $divisionType= $this->division['type'];

        if (in_array($divisionType, self::ALLOWED_DIVISION_TYPES) &&
            in_array($legalEntityType, self::ALLOWED_LEGAL_ENTITY_TYPES)
        ) {
            return true;
        }

        $this->setMessage(__('validation.attributes.healthcareService.error.division.mapping'));

        return false;
    }
}
