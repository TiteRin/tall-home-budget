<?php

namespace App\Livewire\Home;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class MovementsList extends Component
{
    public array $members = [];

    public function render(): View
    {
        if (count($this->members) === 0) {
            return view('livewire.home.movements-list-empty');
        }
        return view('livewire.home.movements-list');
    }
}
