<?php

namespace App\Livewire;

use App\Exceptions\Households\MismatchedHouseholdException;
use App\Models\Household;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class Home extends Component
{
    public Household $household;

    private $bills;
    public array $incomes = [];

    protected $listeners = [
        'incomeModified' => 'onIncomeModified'
    ];

    public function mount(Household $household): void
    {
        $this->household = $household;
    }

    public function render(): View
    {
        $household = $this->household;
        $members = $this->household->members->all();
        $bills = $this->household->bills->all();

        return view('livewire.home.home', compact('household', 'members', 'bills'));
    }

    /**
     * @throws MismatchedHouseholdException
     */
    public function onIncomeModified(int $memberId, ?int $amount): void
    {
        if (!$this->household->members->contains('id', $memberId)) {
            throw new MismatchedHouseholdException();
        }

        if ($amount === null) {
            unset($this->incomes[$memberId]);
            return;
        }

        $this->incomes[$memberId] = $amount;
    }


}
