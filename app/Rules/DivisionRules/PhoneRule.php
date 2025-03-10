<?php

namespace App\Rules\DivisionRules;

use Illuminate\Contracts\Validation\ValidationRule;
use App\Exceptions\CustomValidationException;
use Closure;

class PhoneRule implements ValidationRule
{
    const PHONE_RULES_LIST = [
        'checkPhoneType',
        'checkNumber'
    ];

    protected string $message;

    protected array $dictionaries;

    protected array $division;

    public function __construct(array $division)
    {
        $this->division = $division;

        $this->message = __('validation.attributes.healthcareService.error.division.phone.commonError');
    }

    /**
     * Phone rules validation
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        foreach (self::PHONE_RULES_LIST as $check) {
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
     * Check that phone type exists in dictionaries. PHONE_TYPE required (MOBILE,LAND_LINE)
     *
     * @return bool
     */
    protected function checkPhoneType(): bool
    {
        $phoneType= $this->division['phones']['type'];
        $dictionary = dictionary()->getDictionary('PHONE_TYPE');

        if (!in_array($phoneType, array_keys($dictionary))) {
            $this->setMessage(__('validation.attributes.healthcareService.error.division.phone.type'));

            return false;
        }

        return true;
    }

    /**
     * Check that phone number is valid according to international rules
     *
     * @return bool
     */
    protected function checkNumber(): bool
    {
        $phoneNumber= $this->division['phones']['number'];

        if (!preg_match('/^\\+38[0-9]{10}$/', $phoneNumber)) {
            $this->setMessage(__('validation.attributes.healthcareService.error.division.phone.number'));

            return false;
        }

        return true;
    }
}
