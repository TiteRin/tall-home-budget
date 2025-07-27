<?php

namespace App\Livewire;

use App\Enums\DistributionMethod;
use Illuminate\View\View;
use Livewire\Component;

use App\Traits\HasCurrencyFormatting;

class BillForm extends Component
{

    use HasCurrencyFormatting;

    public array $distributionMethods = [];
    public array $householdMembers = [];
    public bool $hasJointAccount = true;

    public string $newName = '';
    public int|null $newAmount = null;
    public DistributionMethod $newDistributionMethod = DistributionMethod::EQUAL;
    public int|null $newMemberId = null;


    public function render(): View
    {
        return view('livewire.bill-form');
    }

    public function getFormattedNewAmount(): string {
        return $this->formatCurrency($this->newAmount);
    }
}
