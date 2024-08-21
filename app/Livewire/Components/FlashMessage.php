<?php

namespace App\Livewire\Components;

use Livewire\Component;

class FlashMessage extends Component
{

    public string $message = '';

    public string $type = 'success';

    public  array  $errors = [];

    protected $listeners = ['flashMessage'];

    public function flashMessage($flash)
    {
        $this->message = $flash['message'] ?? '';
        $this->type = $flash['type'];
        $this->errors = $flash['errors'] ?? [];
    }


    public function render()
    {
        return view('livewire.components.flash-message');
    }
}
