<?php

declare(strict_types=1);

namespace App\Enums;

enum Status: string
{
    case NEW = 'NEW';
    case APPROVED = 'APPROVED';
    case REJECTED = 'REJECTED';
    case SIGNED = 'SIGNED';
    case DISMISSED = 'DISMISSED';

    public function label(): string
    {
        return match ($this) {
            self::NEW => 'Новий',
            self::APPROVED => 'Підтверджено',
            self::REJECTED => 'Відхилено',
            self::SIGNED => 'Підписано',
            self::DISMISSED => 'Звільнено',
        };
    }
}
