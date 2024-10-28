<?php

namespace App\Livewire\Components\Declaration;

use Livewire\Component;

class DeclarationsFilter extends Component
{

    public $declarations_filter = [
        'first_name' => '',
        'last_name' => '',
        'second_name' => '',
        'declaration_number' => '',
        'phone' => '',
        'birth_date' => '',
    ];

    public function updated($field)
    {
        $this->dispatch('searchUpdated', $this->declarations_filter);
    }


    public function render()
    {
        return view('livewire.components.declaration.declarations-filter');
    }
}
