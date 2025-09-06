<?php

namespace App\Livewire;

use App\Models\Household;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Home extends Component
{
    #[Prop]
    public Household $household;

    public $members;
    public $bills;
    public array $incomes = [];

    public function mount(): void
    {
        $this->members = $this->household->members;
        $this->bills = $this->household->bills;
    }

    public function render(): View
    {
        $household = $this->household;
        return view('livewire.home.home', compact('household'));
    }
}
