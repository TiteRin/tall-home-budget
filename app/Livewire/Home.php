<?php

namespace App\Livewire;

use App\Exceptions\Households\MismatchedHouseholdException;
use App\Models\Household;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Home extends Component
{
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

    /**
     * @throws MismatchedHouseholdException
     */
    public function onIncomeModified(int $memberId, ?int $amount): void
    {
        if (!$this->members->contains('id', $memberId)) {
            throw new MismatchedHouseholdException();
        }

        if ($amount === null) {
            unset($this->incomes[$memberId]);
            return;
        }

        $this->incomes[$memberId] = $amount;
    }


}
