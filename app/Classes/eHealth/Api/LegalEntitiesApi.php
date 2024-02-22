<?php

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\Request;

class LegalEntitiesApi extends Request
{


    public const URL = '/v2/legal_entities';

    public static function _get($params = []): array
    {
        return self::get(self::URL, $params);
    }

    public static function _createOrUpdate($params = []): array
    {
        return self::put(self::URL, $params);
    }

}
