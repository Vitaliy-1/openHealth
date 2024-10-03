<?php

namespace App\Services;

use App\Models\LegalEntity;

class LegalEntityService
{
    public LegalEntity $legalEntity;


    public function __construct( LegalEntity $legalEntity)
    {

        $this->legalEntity = $legalEntity;
    }




}
