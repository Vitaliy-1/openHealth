<?php

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\Request;

class PersonApi extends Request
{


    public const URL = '/api/clients';

    public static function _getAuthMethod(array $params = []): array
    {
        //050b5d17-3bd9-4230-b707-b4528bfe0afc
        return (new Request('GET', self::URL, $params))->sendRequest();
    }

}
