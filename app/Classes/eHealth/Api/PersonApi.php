<?php

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\Request;

class PersonApi extends Request
{


    public const URL = '/api/persons';

    public static function _getAuthMethod(): array
    {

        $data = [
            'page' => 2,
        ];

        return (new Request('get', self::URL,$data,true))->sendRequest();


    }

}
