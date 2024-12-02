<?php

namespace App\Livewire\Employee\Forms\Api;

use App\Classes\eHealth\Api\EmployeeApi;
use App\Classes\eHealth\Services\SchemaService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class EmployeeRequestApi extends EmployeeApi
{

    public static function getEmployees($legal_entity_id):array
    {
        $params = [
            'legal_entity_id' => $legal_entity_id ,
            'page' => 1,
            'page_size' => 300
        ];
        return self::_get($params);
    }

    public static function createEmployeeRequest($data):array
    {
        return self::_create($data);
    }

    public static function getEmployeeById($id): array
    {
        return self::_getById($id);
    }

    public static function dismissedEmployeeRequest($id):array
    {
        return self::_dismissed($id);
    }
    /*
     * @arg array $data
     */
    public static function getEmployeeRequestsList($data = []):array
    {
        return self::_getRequestList($data);
    }

    //Get employee request by id
    public static function getEmployeeRequestById($id):array
    {
        return self::_getRequestById($id);
    }


}
