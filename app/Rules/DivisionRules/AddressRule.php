<?php

namespace App\Rules\DivisionRules;

use Illuminate\Contracts\Validation\ValidationRule;
use App\Exceptions\CustomValidationException;
use Closure;

class AddressRule implements ValidationRule
{
    const ALLOWED_LEGAL_ENTITY_TYPES = ['PRIMARY_CARE', 'MSP', 'MSP_PHARMACY'];

    const ALLOWED_DIVISION_TYPES = ['CLINIC', 'AMBULANT_CLINIC', 'FAP'];

    const ADDRESS_RULES_LIST = [
        'checkAddressType',
        'checkSettlementType',
        'checkStreetType',
        'checkZipCode',
        'checkMapping'
    ];

    protected string $message;

    protected array $dictionaries;

    protected array $division;

    public function __construct(array $division)
    {
        $this->division = $division;

        $this->message = __('validation.attributes.healthcareService.error.division.address.commonError');

        $this->dictionaries = dictionary()->getDictionaries(['ADDRESS_TYPE', 'SETTLEMENT_TYPE', 'STREET_TYPE'], true);
    }

    /**
     * Check that all bunch of the address' data is correct and valid
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        foreach (self::ADDRESS_RULES_LIST as $check) {
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
     * Check that addresses.type exists in dictionaries
     *
     * @return bool
     */
    protected function checkAddressType(): bool
    {
        $addressType= $this->division['addresses']['type'];

        if (!in_array($addressType, array_keys($this->dictionaries['ADDRESS_TYPE']))) {
            $this->setMessage(__('validation.attributes.healthcareService.error.division.address.type'));

            return false;
        }

        return true;
    }

    /**
     * Check that addresses.settlement_type exists in dictionaries
     *
     * @return bool
     */
    protected function checkSettlementType(): bool
    {
        $settlementType = $this->division['addresses']['settlement_type'];

        if (!in_array($settlementType, array_keys($this->dictionaries['SETTLEMENT_TYPE']))) {
            $this->setMessage(__('validation.attributes.healthcareService.error.division.address.settlementType'));

            return false;
        }

        return true;
    }

    /**
     * Check that addresses.street_type exists in dictionaries
     *
     * @return bool
     */
    protected function checkStreetType(): bool
    {
        $streetType= $this->division['addresses']['street_type'];

        if (!in_array($streetType, array_keys($this->dictionaries['STREET_TYPE']))) {
            $this->setMessage(__('validation.attributes.healthcareService.error.division.address.streetType'));

            return false;
        }

        return true;
    }

    /**
     * Check that addresses.zip has no more than 5 digits
     *
     * @return bool
     */
    protected function checkZipCode(): bool
    {
        $zipCode= $this->division['addresses']['zip'];

        if (!preg_match('/^[0-9]{5}$/', $zipCode)) {
            $this->setMessage(__('validation.attributes.healthcareService.error.division.address.zip'));

            return false;
        }

        return true;
    }

    /**
     * Check mapping legal_entity_type, division_type and address_type and its obligation
     *
     * @return bool
     */
    protected function checkMapping(): bool
    {
        $legalEntityType =auth()->user()->legalEntity->type;
        $divisionType= $this->division['type'];
        $addressType= $this->division['addresses']['type'];

        if (in_array($divisionType, self::ALLOWED_DIVISION_TYPES) &&
            in_array($legalEntityType, self::ALLOWED_LEGAL_ENTITY_TYPES) &&
            $addressType === 'RESIDENCE'
        ) {
            return true;
        }

        $this->setMessage(__('validation.attributes.healthcareService.error.address.mapping'));

        return false;
    }
}
