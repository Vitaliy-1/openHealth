<?php

namespace App\Enums\Person;

use Illuminate\Support\Facades\Lang;

enum AuthenticationMethod: string
{
    case OTP = 'OTP';
    case OFFLINE = 'OFFLINE';
    case THIRD_PERSON = 'THIRD_PERSON';

    public function label(): string
    {
        return match ($this) {
            self::OTP => Lang::get('patients.authentication_method.otp'),
            self::OFFLINE => Lang::get('patients.authentication_method.offline'),
            self::THIRD_PERSON => Lang::get('patients.authentication_method.third_person'),
        };
    }

    public static function getOptions(): array
    {
        return array_map(fn($case) => $case->label(), self::cases());
    }
}
