<?php

namespace App\Enums\Person;

use Illuminate\Support\Facades\Lang;

enum RelationType: string
{
    case PRIMARY = 'PRIMARY';
    case SECONDARY = 'SECONDARY';

    public function label(): string
    {
        return match ($this) {
            self::PRIMARY => Lang::get('patients.relation_type.primary'),
            self::SECONDARY => Lang::get('patients.relation_type.secondary'),
        };
    }

    public static function getOptions(): array
    {
        return array_map(fn($case) => $case->label(), self::cases());
    }
}
