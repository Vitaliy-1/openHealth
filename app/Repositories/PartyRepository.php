<?php

namespace App\Repositories;

use App\Models\Relations\Party;
use Exception;

class PartyRepository
{
    /**
     * Creates a new Party record.
     *
     * @param array $data
     * @return Party
     */
    public function create(array $data): Party
    {
        try {
            return Party::create($data);
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Updates an existing Party record.
     *
     * @param Party $party
     * @param array $data
     * @return Party
     */
    public function update(Party $party, array $data): Party
    {
        try {
            $party->update($data);
            return $party;
        } catch (Exception $e) {
            throw $e;
        }
    }
}
