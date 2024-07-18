<?php

namespace App\Livewire\Components;

use Livewire\Component;

class FlashMessage extends Component
{

    public string $message = '';
    public string $type = 'success';

    protected $listeners = ['flashMessage'];

    public function flashMessage($flash)
    {
        $this->message = $flash['message'];
        $this->type = $flash['type'];

    }


    public function render()
    {
        return view('livewire.components.flash-message');
    }
}
