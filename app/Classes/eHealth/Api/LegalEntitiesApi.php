<?php

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\Exceptions\ApiException;
use App\Classes\eHealth\Request;

class LegalEntitiesApi extends Request
{


    public const URL = '/api/v2/legal_entities';

    public static function _get($params = []): array
    {
       return (new Request('GET', self::URL, $params))->sendRequest();
    }

    public static function _createOrUpdate($params = []): array
    {
        return (new Request('PUT', self::URL, $params))->sendRequest();
    }


}
