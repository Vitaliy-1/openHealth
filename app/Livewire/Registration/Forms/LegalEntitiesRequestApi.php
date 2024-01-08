<?php

namespace App\Livewire\Registration\Forms;

use App\Classes\eHealth\Api\LegalEntitiesApi;
use Livewire\Component;

class LegalEntitiesRequestApi extends Component
{

    public function get($edrpou): array
    {
        $legalEntitiesApi = (new LegalEntitiesApi())->getLegalEntities(['edrpou' => $edrpou]);

        return !empty($legalEntitiesApi[0]) ? $legalEntitiesApi[0] : [];
    }

    public function createOrUpdate($data)
    {
        ///signed_content_encoding == base64_encode , signed_legal_entity_request

        $legalEntitiesApi = (new LegalEntitiesApi())->createOrUpdateLegalEntities($data);

        return !empty($legalEntitiesApi[0]) ? $legalEntitiesApi[0] : [];
    }


}
