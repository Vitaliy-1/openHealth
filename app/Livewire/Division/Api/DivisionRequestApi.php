<?php

namespace App\Livewire\Division\Api;

use App\Classes\eHealth\Api\DivisionApi;
use App\Services\DivisionApiService;

class DivisionRequestApi extends DivisionApi
{
    public static ?DivisionApiService $apiService = null;

    public static function getApiService(): DivisionApiService
    {
        if (self::$apiService === null) {
            self::$apiService = new DivisionApiService();
        }

        return self::$apiService;
    }

    public static function getDivisionRequest($params = []):array
    {
        return self::_get($params);
    }

    public static function createDivisionRequest($data): array
    {
        $service = self::getApiService();

        $params = self::$apiService->prepareRequest($data);

        $response = self::_create($params);

        return $service->prepareResponse($response);
    }

    public static function updateDivisionRequest($id, $data): array
    {
        $service = self::getApiService();

        $params = $service->prepareRequest($data);

        $response = self::_update($id, $params);

        return $service->prepareResponse($response);
    }

    public static function deactivateDivisionRequest($id):array
    {
        return self::_deactivate($id);
    }

    public static function activateDivisionRequest($id):array
    {
        return self::_activate($id);
    }

    public static function syncDivisionRequest($legal_entity_id):array
    {
        return self::_sync($legal_entity_id);
    }
}
