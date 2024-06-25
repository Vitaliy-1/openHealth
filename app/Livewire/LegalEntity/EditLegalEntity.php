<?php

namespace App\Livewire\LegalEntity;

use App\Classes\eHealth\Api\LegalEntitiesApi;
use App\Classes\eHealth\Api\oAuthEhealth\oAuthEhealth;
use App\Classes\eHealth\Api\PersonApi;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EditLegalEntity extends Component
{

    public function render()
    {
        dd(oAuthEhealth::getUser());
        dd(LegalEntitiesApi::_getById(Auth::user()->legalEntity->uuid));

        return view('livewire.legal-entity.edit-legal-entity');
    }

}
