<?php

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\Request;

class LegalEntitiesApi extends Request
{


    public const URL_V2 = '/api/v2/legal_entities';
    public const URL = '/api/legal_entities';


    public static function _get(array $params = []): array
    {
       return (new Request('GET', self::URL_V2, $params,false))->sendRequest();
    }

    public static function _getById(string $id): array
    {
        $params = [
            'legal_entity_id' => $id
        ];
        return (new Request('GET', self::URL_V2.'/'.$id,$params))->sendRequest();
    }

    public static function _verify(string $id): array{
        return (new Request('PATCH', self::URL.'/'.$id.'/actions/nhs_verify',[]))->sendRequest();
    }

    public static function _createOrUpdate(array $params = []): array
    {
        return (new Request('PUT', self::URL_V2, $params,false))->sendRequest();
    }





}
