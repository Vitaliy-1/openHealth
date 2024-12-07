<?php

namespace App\Repositories;

use App\Models\Relations\Party;

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
                'uuid' => $data['id']
            ],
            $data
        );
    }
}
