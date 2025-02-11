<?php

declare(strict_types=1);

namespace App\Livewire\Components;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class FlashMessage extends Component
{
    public string $message = '';

    public string $type = 'success';

    public array $errors = [];

    protected $listeners = ['flashMessage'];

    public function flashMessage($flash): void
    {
        $this->message = $flash['message'] ?? '';
        $this->type = $flash['type'];
        $this->errors = $flash['errors'] ?? [];
    }

    public function render(): View
    {
        return view('livewire.components.flash-message');
    }
}
