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
        $uuid = $data['uuid'] ?? Str::uuid();
        return Party::updateOrCreate(
            [
                'uuid' => $uuid,
            ],
            $data
        );
    }
}
