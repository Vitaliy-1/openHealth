<?php

namespace App\Repositories;

use Exception;
use App\Models\Division;
use App\Models\HealthcareService;
use Illuminate\Support\Facades\DB;
use App\Classes\eHealth\Api\HealthcareService as HealthcareServiceApi;

class HealthcareServiceRepository
{
    protected ?Division $division = null;

    /**
     * Sets the division for this healthcare service
     *
     * @param Division $division The division to set
     *
     * @return static Returns this repository instance for method chaining
     */
    public function setDivision(Division $division): static
    {
        $this->division = $division;

        return $this;
    }

    public function getDivision(): Division
    {
        return $this->division;
    }


    /**
     * Saves a list of healthcare services.
     *
     * @param array $responseList The list of healthcare services to be saved.
     *
     * @return void
     */    public function saveHealthcareServiceList($responseList): void
    {
        DB::transaction(function () use ($responseList) {
            foreach ($responseList as $responseItem) {
                $this->saveHealthcareServiceResponseData($responseItem);
            }
        });
    }

    /**
     * Saves all healthcare services from API response using batch upsert operation.
     *
     * @param array $healthcareServicesList Raw healthcare services data from eHealth API
     *
     * @return void
     *
     * @throws Exception If database transaction fails
     */
    public function saveHealthcareServiceAll(array $healthcareServicesList): void
    {
        DB::transaction(function() use($healthcareServicesList) {
            $uspertData = HealthcareServiceApi::normalizeResponseDataForUpsert($healthcareServicesList);

            // At first save all the Divisions to teh DB
            HealthcareService::upsert($uspertData, uniqueBy: ['uuid'], update: new HealthcareService()->getFillable());
        });
    }

    /**
     * TODO: maybe need to put it into validation (need testing)
     * Prepare Request Data
     *
     * @param mixed $rawData
     *
     * @return array
     */
    public function prepareRequestCreateData(array $rawData): array
    {
         $params = [
            'division_id' => $this->getDivision()->uuid,
            'category' => [
                'coding' => [
                    [
                        'system' => 'HEALTHCARE_SERVICE_CATEGORIES',
                        'code' => $rawData['category']
                    ]
                ]
            ],
            'providing_condition' => $rawData['providing_condition'],
            'speciality_type' => $rawData['speciality_type'],
        ];


        if (isset($rawData['comment']) && !empty($rawData['comment'])) {
            $params['comment'] = $rawData['comment'];
        }

        if (!empty($rawData['available_time'])) {
            foreach ($rawData['available_time'] as $index => $dayTime) {
                if (!empty($dayTime['all_day'])) {
                    $rawData['available_time'][$index]['available_start_time'] = '';
                    $rawData['available_time'][$index]['available_end_time'] = '';
                }
            }

            $params['available_time'] = available_time($rawData['available_time']);
        }

        if (!empty($rawData['not_available'])) {
            $params['not_available'] = not_available($rawData['not_available']);
        }

        return $params;
    }

    /**
     * Prepares the raw data for a healthcare service update request.
     * For update, to modify only allowed 'comment', 'available_time' and 'not_available' fields.
     *
     * @param array $rawData The raw data to be processed for the update request
     *
     * @return array The processed data ready for updating a healthcare service
     */
    public function prepareRequestUpdateData(array $rawData): array
    {
        $params = [];

        if (!empty($rawData['comment'])) {
            $params['comment'] = $rawData['comment'];
        }

        if (!empty($rawData['available_time'])) {
            foreach ($rawData['available_time'] as $index => $dayTime) {
                if (!empty($dayTime['all_day'])) {
                    $rawData['available_time'][$index]['available_start_time'] = '';
                    $rawData['available_time'][$index]['available_end_time'] = '';
                }
            }

            $params['available_time'] = available_time($rawData['available_time']);
        }

        if (!empty($rawData['not_available'])) {
            $params['not_available'] = not_available($rawData['not_available']);
        }

        return $params;
    }

    /**
     * Set status for specific action (for activate or deactivate)
     *
     * @param \App\Models\HealthcareService $healthcareService
     * @param string $status
     *
     * @throws \Exception
     *
     * @return void
     */
    public function setAction(HealthcareService $healthcareService, string $status): void
    {
        try {
            $healthcareService->setAttribute('status', $status)->save();

            $healthcareService->refresh();

        } catch (Exception $err) {
            throw new Exception($err->getMessage());
        }
    }

    /**
     * Create instance of Healthcare Service class
     *
     * @param array $responseData // The data array suitable to do fill on HealthcareService Model
     *
     * @return HealthcareService|null
     */
    public function createOrUpdate(array $responseData): HealthcareService|null
    {
        $healthcareService = HealthcareService::firstOrNew(['uuid' => $responseData['uuid']]);

        $healthcareService->fill($responseData);

        return $healthcareService;
    }

    /**
     * Create instance of HealthcareService model and save it's data to the DB (with all it's relations aka: Phone)
     *
     * @param array $divisionData
     * @param \App\Models\LegalEntity $legalEntity
     * @return HealthcareService
     */
    public function saveHealthcareServiceResponseData(array $responseData): HealthcareService
    {
        $division = $this->getDivision();

        $healthcareService = $this->createOrUpdate($responseData);

        $division->healthcareService()->save($healthcareService);

        $healthcareService->refresh();

        return $healthcareService;
    }
}
