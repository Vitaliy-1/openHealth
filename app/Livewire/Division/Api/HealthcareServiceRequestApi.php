<?php

namespace App\Livewire\Division\Api;

use App\Classes\eHealth\Api\HealthcareServiceApi;

class HealthcareServiceRequestApi extends HealthcareServiceApi
{

    public function __construct( )
    {
        parent::__construct();
    }

    public static function getHealthcareServiceRequest($params = []): array
    {
        return self::_get($params);
    }

    public static function getHealthcareServiceRequestById($id, $params = []): array
    {
        return  self::_getById($id, $params);
    }

    public static function createHealthcareServiceRequest($data): array
    {
        return  self::_create($data);
    }

    public static function updateHealthcareServiceRequest($id, $data): array
    {
        return  self::_update($id, $data);
    }

    public static function deactivateHealthcareServiceRequest($id): array
    {
        return  self::_deactivate($id);
    }

    public static function activateHealthcareServiceRequest($id): array
    {
        return  self::_activate($id);
    }
}
