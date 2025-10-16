<?php

namespace App\Livewire\Home;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class MovementsList extends Component
{
    #[Locked]
    public array $members = [];
    #[Locked]
    public array $incomes = [];
    #[Locked]
    public array $bills = [];

    public function render(): View
    {
        if (count($this->members) === 0) {
            return view('livewire.home.movements-list-empty');
        }

        if (count($this->bills) === 0) {
            return view('livewire.home.movements-list-empty');
        }

        return view('livewire.home.movements-list');
    }
}
