<?php

namespace App\Livewire;

use App\Models\Member;
use Illuminate\View\View;
use Livewire\Component;

class BillsManager extends Component
{
    public function render(): View
    {
        return view(
            'livewire.bills-manager',
            [
                'members' => Member::all(),
            ]
        );
    }
}
