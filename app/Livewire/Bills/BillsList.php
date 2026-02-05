<?php

namespace App\Livewire\Bills;

use App\Domains\ValueObjects\Amount;
use App\Models\Bill;
use App\Models\Member;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class BillsList extends Component
{

    public array $bills = [];
    public array $expenseTabs = [];

    public array $members = [];

    public function render(): View
    {
        if (count($this->bills) === 0 && count($this->expenseTabs) === 0) {
            return view('livewire.bills.bills-list-empty');
        }

        return view('livewire.bills.bills-list');
    }

    #[Computed]
    public function totalAmountForMember(Member $member)
    {

        $filteredBills = array_filter($this->bills, function (Bill $bill) use ($member) {
            return $bill->member_id === $member->id;
        });

        $total = array_reduce(
            array_column($filteredBills, 'amount'),
            function (Amount $carry, Amount $amount) {
                return $carry->add($amount);
            },
            new Amount(0)
        );

        foreach ($this->expenseTabs as $expenseTab) {
            $total = $total->add($expenseTab->getExpensesForCurrentPeriod()->getTotalForMember($member));
        }

        return $total;
    }

    #[Computed]
    public function totalAmount(): Amount
    {
        $total = array_reduce(
            array_column($this->bills, 'amount'),
            function (Amount $carry, Amount $amount) {
                return $carry->add($amount);
            },
            new Amount(0)
        );

        foreach ($this->expenseTabs as $expenseTab) {
            $total = $total->add($expenseTab->getTotalAmountForCurrentPeriod());
        }

        return $total;
    }
}
