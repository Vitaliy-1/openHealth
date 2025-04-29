<?php

declare(strict_types=1);

namespace App\Repositories\MedicalEvents;

use App\Models\MedicalEvents\Sql\Identifier as SqlIdentifier;

class IdentifierRepository extends BaseRepository
{
    /**
     * Create identifier in DB.
     *
     * @param  string  $value
     * @return SqlIdentifier
     */
    public function store(string $value): SqlIdentifier
    {
        return $this->model::create(['value' => $value]);
    }
}
