<?php

declare(strict_types=1);

namespace App\Enums;

enum JobStatus: string
{
    case PENDING = 'PENDING';
    case SUSPENDED = 'SUSPENDED';
    case PROCESSING = 'PROCESSING';
    case PAUSED = 'PAUSED';
    case COMPLETED = 'COMPLETED';
    case FAILED = 'FAILED';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Очікується',
            self::SUSPENDED => 'Призупинено',
            self::PROCESSING => 'Обробляється',
            self::PAUSED => 'На паузі',
            self::COMPLETED => 'Виконано',
            self::FAILED => 'Помилка'
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
