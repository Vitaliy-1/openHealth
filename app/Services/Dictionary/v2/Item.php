<?php

declare(strict_types=1);

namespace App\Services\Dictionary\v2;

readonly class Item
{
    public function __construct(
        public string $code,
        public string $description,
        public bool $isActive
    ) {
    }
}
