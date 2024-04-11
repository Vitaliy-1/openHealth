<?php

namespace App\Livewire\LegalEntity\Forms;

use App\Classes\eHealth\Api\LegalEntitiesApi;
use Livewire\Component;

class LegalEntitiesRequestApi extends LegalEntitiesApi
{

    public static function getLegalEntitie($edrpou): array
    {
        $legalEntitiesApi = self::_get(['edrpou' => $edrpou]);

        return !empty($legalEntitiesApi[0]) ? $legalEntitiesApi[0] : [];
    }


    public static function getLegalEntities($edrpou): array
    {
        $legalEntitiesApi = self::_get(['edrpou' => $edrpou]);

        return !empty($legalEntitiesApi) ? $legalEntitiesApi : [];
    }

    public static function createOrUpdate($data)
    {
        ///signed_content_encoding == base64_encode , signed_legal_entity_request
        $legalEntitiesApi = self::_createOrUpdate($data);

        return !empty($legalEntitiesApi) ? $legalEntitiesApi : [];
    }


}
