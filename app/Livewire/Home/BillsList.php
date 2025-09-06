<?php

namespace App\Livewire\Home;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class BillsList extends Component
{

    public array $bills = [];

    public function render(): View
    {
        if (count($this->bills) === 0) {
            return view('livewire.home.bills-list-empty');
        }

        return view('livewire.home.bills-list');
    }
}
