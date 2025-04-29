<?php

declare(strict_types=1);

namespace App\Repositories\MedicalEvents;

use App\Models\MedicalEvents\Sql\Coding as SqlCoding;

class CodingRepository extends BaseRepository
{
    /**
     * Crate coding in DB by provided data.
     *
     * @param  array  $coding
     * @return SqlCoding
     */
    public function store(array $coding): SqlCoding
    {
        return $this->model::create([
            'system' => $coding['system'],
            'code' => $coding['code']
        ]);
    }
}
