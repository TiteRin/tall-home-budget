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
    public int $newAmount;
    public string $formattedNewAmount;
    public DistributionMethod $newDistributionMethod;
    public int|null $newMemberId;

    public function render(): View
    {
        return view('livewire.bill-form');
    }

    public function updatedFormattedNewAmount($newAmount): void
    {
        $this->newAmount = (int)round((float)$newAmount * 100);
        $this->formattedNewAmount = $this->formatCurrency($this->newAmount);
    }
}
