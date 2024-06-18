<?php

namespace App\Livewire\LegalEntity;

use App\Classes\eHealth\Api\PersonApi;
use Livewire\Component;

class EditLegalEntity extends Component
{

    public function render()
    {
        dd(PersonApi::_getAuthMethod(['legal_entity_id'=>'f13ab4b7-1167-4215-9fb3-2116b775ddb1']));

        return view('livewire.legal-entity.edit-legal-entity');
    }

}
