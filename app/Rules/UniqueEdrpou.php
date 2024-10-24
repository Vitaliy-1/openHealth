<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\LegalEntity;

class UniqueEdrpou implements ValidationRule
{
    protected ?int $legalEntityId;

    public function __construct(?int $legalEntityId = null)
    {
        // Set legal entity id for current user
        $this->legalEntityId = auth()->check() ? auth()->user()->legalEntity->id : null;
    }

    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check if value already exists
        $exists = LegalEntity::where('edrpou',  $this->legalEntityId)
            ->where('id', '<>', $this->legalEntityId) // Exclude current legal entity
            ->exists();

        // If exists
        if ($exists) {
            $fail('Поле :attribute вже зареєстровано в системі.'); // Message for validation
        }
    }
}
