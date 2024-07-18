<?php

namespace App\Livewire\Division\Api;

use App\Classes\eHealth\Api\HealthcareServiceApi;

class HealthcareServiceRequestApi extends HealthcareServiceApi
{


    public static function getHealthcareServiceRequest($params = []): array
    {
        return self::_get($params);
    }

    public static function getHealthcareServiceRequestById($id, $params = []): array
    {
        return self::_getById($id, $params);
    }

    public static function createHealthcareServiceRequest($division_uuid, $data): array
    {
        $params = [
            'division_id'         => $division_uuid,
            'category'            => [
                'coding' => [
                    [
                        'system' => 'HEALTHCARE_SERVICE_CATEGORIES',
                        'code'   => $data['category']
                    ]
                ]
            ],
            'providing_condition' => $data['providing_condition'],
            'speciality_type'     => $data['speciality_type'],
        ];


        if (isset($data['comment']) && !empty($data['comment'])) {
            $params['comment'] = $data['comment'];
        }

        if (isset($data['available_time']) && !empty($data['available_time'])) {
            $params['available_time'] =  available_time($data['available_time']);
        }

        if (isset($data['not_available']) && !empty($data['not_available'])) {
            $params['not_available'] = not_available($data['not_available']);
        }

        return self::_create($params);
    }

    public static function updateHealthcareServiceRequest($id, $data): array
    {
        $params = [];

        if (isset($data['comment']) && !empty($data['comment'])) {
            $params['comment'] = $data['comment'];
        }

        if (isset($data['available_time']) && !empty($data['available_time'])) {
            $params['available_time'] = available_time($data['available_time']);
        }
        if (isset($data['not_available']) && !empty($data['not_available'])) {
            $params['not_available'] = not_available($data['not_available']);
        }

        if (!empty($params)) {
            return self::_update($id, $params);
        }

        return $data;
    }

    public static function deactivateHealthcareServiceRequest($id): array
    {
        return self::_deactivate($id);
    }

    public static function activateHealthcareServiceRequest($id): array
    {

        return self::_activate($id);
    }


    public static function syncHealthcareServiceRequest($division_uuid): array
    {
        $params = [
            'division_id' => $division_uuid,
            'page'        => 1,
            'page_size'   => 100
        ];

        return self::_get($params);
    }
}
