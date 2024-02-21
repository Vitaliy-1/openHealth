<?php

namespace App\Classes\eHealth\Api;

use App\Classes\eHealth\Request;

class LegalEntitiesApi
{


    private string $getV2 = '/v2/legal_entities';

    public function getLegalEntities($params = []): array
    {
        return Request::get($this->getV2, $params);
    }

    public function createOrUpdateLegalEntities($params = []): array
    {
        return Request::put($this->getV2, $params);
    }
}
