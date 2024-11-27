<?php

namespace App\Rules\DivisionRules;

use Illuminate\Contracts\Validation\ValidationRule;
use App\Exceptions\CustomValidationException;
use Closure;

class EmailRule implements ValidationRule
{
    protected string $message;

    protected array $dictionaries;

    protected array $division;

    public function __construct(array $division)
    {
        $this->division = $division;
    }

    /**
     * Check that Email has a valid format and specified correctly
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $email= $this->division['email'];

        if (!preg_match("/^[\w!#$%&'*+\/=?`{|}~^-]+(?:\.[\w!#$%&'*+\/=?`{|}~^-]+)*@(?:[a-z0-9-]+\.)+[a-z]{2,}$/i", $email)) {
            throw new CustomValidationException($this->message(), 'custom');
        }
    }

    protected function message(): string
    {
        return __('validation.attributes.healthcareService.error.division.email');
    }
}
