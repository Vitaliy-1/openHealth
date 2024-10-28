<?php

namespace App\Enums\Declaration;

enum DeclarationStatus: string
{


    case ACTIVE = 'NEW';
    case APPROVED = 'APPROVED';
    case REJECTED = 'REJECTED';
    case SIGNED = 'SIGNED';

    public function label(): string
    {
        return match ($this) {
            self::ACTIVE => 'Новий',
            self::APPROVED => 'Підтверджено',
            self::REJECTED => 'Відхилено',
            self::SIGNED => 'Підписано',
        };
    }


}
