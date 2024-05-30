<?php

namespace App\Classes\eHealth\Api;


use App\Classes\eHealth\Request;

class AdressesApi
{

    public const URL_REGIONS = '/addresses/regions';


    public static function _regions($params = []): array
    {
        return (new Request('get', self::URL_REGIONS, $params,false))->sendRequest();
    }
}
