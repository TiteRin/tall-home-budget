<?php

namespace App\Livewire\Home;

use App\Domains\ValueObjects\Amount;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
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

    #[Computed]
    public function totalAmount(): Amount
    {
        return array_reduce(
            array_column($this->bills, 'amount'),
            function (Amount $carry, Amount $amount) {
                return $carry->add($amount);
            },
            new Amount(0)
        );
    }
}
