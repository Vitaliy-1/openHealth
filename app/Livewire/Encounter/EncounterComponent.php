<?php

declare(strict_types=1);

namespace App\Livewire\Encounter;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use App\Livewire\Encounter\Forms\Encounter as EncounterForm;

class Encounter extends Component
{
    public EncounterForm $form;

    public function render(): View
    {
        return view('livewire.encounter.encounter');
    }
}
