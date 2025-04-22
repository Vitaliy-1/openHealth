<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class OnlyOnePrimaryDiagnosis implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     * @return void
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $primaryCount = collect($value)->filter(function (array $diagnosis) {
            return collect($diagnosis['role']['coding'] ?? [])
                ->contains('code', 'primary');
        })->count();

        if ($primaryCount !== 1) {
            $fail(__('Тільки один основний діагноз може бути.'));
        }
    }
}
