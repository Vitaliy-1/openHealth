<?php

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\Request;

class EmployeeApi extends Request
{

    public const URL = '/api/v2/employee_requests';


    public static function _get($params): array
    {
        return (new Request('GET', '/api/employees', $params))->sendRequest();
    }

    public static function _create($params = []): array
    {
        return (new Request('POST', self::URL, $params))->sendRequest();
    }


    public static function _dismissed($id): array
    {
        return (new Request('POST', '/api/employees/'.$id.'/actions/deactivate', []))->sendRequest();
    }

    public static function _getRolesById(){

        return (new Request('GET', '/api/employee_roles/796612ee-452b-4933-b522-714bc399a55e', []))->sendRequest();
    }


}
