<?php

namespace App\Livewire;

use App\Models\Household;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Home extends Component
{
    #[Prop]
    public Household $household;

    public array $members;
    public array $bills;
    public array $incomes;

    public function render(): View
    {
        $household = $this->household;
        return view('livewire.home.home');
    }
}
