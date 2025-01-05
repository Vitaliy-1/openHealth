<?php

namespace App\Livewire\Encounter;

use Livewire\Component;
use App\Livewire\Encounter\Forms\Encounter as EncounterForm;

class Encounter extends Component
{
    public EncounterForm $form;

    public function render()
    {
        return view('livewire.encounter.encounter');
    }
}