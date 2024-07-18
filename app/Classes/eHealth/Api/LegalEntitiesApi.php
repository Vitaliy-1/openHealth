<?php

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\Request;

class LegalEntitiesApi extends Request
{


    public const URL = '/api/v2/legal_entities';

    public static function _get(array $params = []): array
    {
       return (new Request('GET', self::URL, $params))->sendRequest();
    }

    public static function _getById(string $id): array
    {
        $params = [
            'legal_entity_id' => $id
        ];
        return (new Request('GET', self::URL.'/'.$id,$params))->sendRequest();
    }

    public static function _createOrUpdate(array $params = []): array
    {
        return (new Request('PUT', self::URL, $params,false))->sendRequest();
    }





}
