<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Person;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class PersonRepository
{
    /**
     * Save person response to DB.
     *
     * @param  array  $response  Response from API
     * @param  string  $personUuid
     * @return bool
     * @throws Throwable
     */
    public function savePersonResponseData(array $response, string $personUuid): bool
    {
        DB::beginTransaction();

        try {
            $this->createOrUpdate($response['data'], $personUuid);

            DB::commit();

            return true;
        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Error saving person request data', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'response' => $response
            ]);

            return false;
        }
    }

    /**
     * Create or update data in DB.
     *
     * @param  array  $data
     * @param  string  $personUuid
     * @return Person
     */
    protected function createOrUpdate(array $data, string $personUuid): Person
    {
        $personRequestData = [
            'uuid' => $personUuid,
            'first_name' => $data['person']['first_name'],
            'last_name' => $data['person']['last_name'],
            'second_name' => $data['person']['second_name'] ?? null,
            'birth_date' => Carbon::parse($data['person']['birth_date'])->format('Y-m-d'),
            'gender' => $data['person']['gender'],
            'tax_id' => $data['person']['tax_id'] ?? null,
            'birth_settlement' => $data['person']['birth_settlement'] ?? null,
            'birth_country' => $data['person']['birth_country'] ?? null,
        ];

        return Person::updateOrCreate(
            [
                'uuid' => $personRequestData['uuid']
            ],
            $personRequestData
        );
    }
}
