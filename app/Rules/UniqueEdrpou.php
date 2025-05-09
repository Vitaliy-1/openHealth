<?php

namespace App\Rules;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\LegalEntity;

class UniqueEdrpou implements ValidationRule
{
    protected ?int $legalEntityId;


    public function __construct()
    {
        /** @var User|null $user */
        $user = auth()->user();

        $this->legalEntityId = $user && $user->legalEntity
            ? $user->legalEntity->id
            : null;

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
        // Check if value already exists in another legal entity, excluding the current entity
        $exists = LegalEntity::where('edrpou', $value)
            ->when($this->legalEntityId !== null, function ($query) {
                $query->where('id', '<>', $this->legalEntityId); //Exclude the current entity
            })
            ->exists();

        // If it exists, fail the validation
        if ($exists) {
            $fail(__('validation.custom.unique_edrpou')); // Message for validation
        }
    }
}
