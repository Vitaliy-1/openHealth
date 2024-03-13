<?php

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\Request;

class EmployeeApi extends Request
{

    public const URL = 'employee_requests';


    public static function _create($params = []): array
    {
        return self::post(self::URL, $params);
    }


}
