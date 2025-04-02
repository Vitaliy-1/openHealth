<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;


class DocumentNumber implements ValidationRule
{
    protected array $dictionary;

    protected string $documentType;

    /**
     * Create a new rule instance.
     *
     * @param  string  $documentType // Name of the Document Type (get from 'DOCUMENT_TYPE' dictionary)
     *
     * @return void
     */
    public function __construct(string $documentType = '')
    {
        $this->documentType = $documentType;

        $this->dictionary = dictionary()->getDictionary('DOCUMENT_TYPE', true);
    }

    /**
     * Check that Email has a valid format and specified correctly
     *
     * @param  \Closure(string): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $regex = match ($this->documentType) {
            'NATIONAL_ID' => '/^[0-9]{9}$/',
            'PASSPORT' => '/^((?![ЫЪЭЁ])([А-ЯҐЇІЄ])){2}[0-9]{6}$/u',
            default => ''
        };

        if (!$regex || !(bool)preg_match($regex, $value)) {
            $fail(__('forms.document') . ' : ' . __('validation.attributes.errors.wrongNumberFormat'));
        }
    }
}
