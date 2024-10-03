<?php

namespace App\Livewire\LegalEntity;


use App\Livewire\LegalEntity\Forms\LegalEntitiesRequestApi;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class EditLegalEntity extends Component
{



    public function render()
    {


        if (Session::get('auth_token')) {
            dd(LegalEntitiesRequestApi::_getById(Auth::user()->legalEntity->uuid));
        }

        return view('livewire.legal-entity.edit-legal-entity');
    }

}
