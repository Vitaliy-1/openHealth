<?php

namespace App\Livewire\Encounter;

use Livewire\Component;
use App\Livewire\Encounter\Forms\Encounter;

class EncounterCreate extends Component
{
    public Encounter $form;

    public function mount()
    {

    }

    public function render()
    {
        return view('livewire.encounter.create');
    }
}
