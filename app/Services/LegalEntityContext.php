<?php

namespace App\Services;

use App\Models\LegalEntity;

class LegalEntityContext
{
    protected ?LegalEntity $legalEntity = null;

    public function set(LegalEntity $legalEntity): void
    {
        $this->legalEntity = $legalEntity;
    }

    public function get(): ?LegalEntity
    {
        return $this->legalEntity;
    }

    public function id(): ?int
    {
        return $this->legalEntity?->id;
    }

    public function uuid(): ?string
    {
        return $this->legalEntity?->uuid;
    }

    public function isSet(): bool
    {
        return !is_null($this->legalEntity);
    }
}
