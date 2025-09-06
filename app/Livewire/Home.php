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

    protected $listeners = [
        'incomeModified' => 'onIncomeModified'
    ];

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

    public function onIncomeModified(int $memberId, ?int $amount): void
    {
        if ($amount === null) {
            unset($this->incomes[$memberId]);
            return;
        }

        $this->incomes[$memberId] = $amount;
    }


}
