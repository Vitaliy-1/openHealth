<?php

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\Request;

class EmployeeApi extends Request
{

    public const URL_REQUEST = '/api/employee_requests';
    public const URL_REQUEST_MIS = '/api/mis/employee_requests';
    public const URL_REQUEST_V2 = '/api/v2/employee_requests';

    public const URL= '/api/employees';



    public static function _get($params): array
    {
        return (new Request('GET', self::URL, $params))->sendRequest();
    }

    public static function _create($params = []): array
    {
        return (new Request('POST', self::URL_REQUEST_V2, $params))->sendRequest();
    }


    public static function _dismissed($id): array
    {
        return (new Request('POST',self::URL.'/'.$id.'/actions/deactivate', []))->sendRequest();
    }

    public static function _getById($id){

        return (new Request('GET',self::URL.'/'.$id, []))->sendRequest();
    }

    public static function _getRequestList($data):array
    {
        return (new Request('GET',self::URL_REQUEST, $data))->sendRequest();
    }

    public static function _getRequestById($id):array
    {
        return (new Request('GET',self::URL_REQUEST.'/'.$id,[] ))->sendRequest();
    }
    public static function _getRequestByIdMis($id):array
    {
        return (new Request('GET',self::URL_REQUEST_MIS.'/'.$id,[] ))->sendRequest();
    }
}
