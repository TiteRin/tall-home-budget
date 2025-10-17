<?php

namespace App\Livewire\Home\Movements;

use App\Domains\ValueObjects\Amount;
use App\Models\Member;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class MovementItem extends Component
{

    public Member $from;
    public Member $to;
    public Amount $amount;

    public function render(): View
    {
        return view('livewire.home.movements.movement-item');
    }
}
