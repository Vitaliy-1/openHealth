<?php

namespace App\Livewire\Components\Declaration;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class DeclarationsFilter extends Component
{
    public array $declarations_filter = [
        'first_name' => '',
        'last_name' => '',
        'second_name' => '',
        'declaration_number' => '',
        'phone' => '',
        'birth_date' => '',
    ];

    public function updated(): void
    {
        $this->dispatch('searchUpdated', $this->declarations_filter);
    }

    public function render(): View
    {
        return view('livewire.components.declaration.declarations-filter');
    }
}
