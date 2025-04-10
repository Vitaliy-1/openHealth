<?php

namespace App\Repositories;

use App\Models\Relations\Party;
use Illuminate\Support\Str;

class PartyRepository
{
    /**
     * @param $data
     * @return Party
     */
    public function createOrUpdate($data): Party
    {
        return Party::updateOrCreate(
            [
                'uuid' => $data['uuid'] ?? null,
            ],
            $data
        );
    }
}
