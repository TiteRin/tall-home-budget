<?php

namespace App\Livewire;

use App\Enums\DistributionMethod;
use App\Models\Member;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

use App\Traits\HasCurrencyFormatting;

class BillForm extends Component
{

    use HasCurrencyFormatting;

    public DistributionMethod $defaultDistributionMethod;
    public Collection $householdMembers;
    public bool $hasJointAccount = true;

    public string $newName = '';
    public int $newAmount;
    public string $formattedNewAmount;
    public string $newDistributionMethod;
    public int|null $newMemberId;

    public function mount(): void
    {
        $this->newDistributionMethod = ($this->defaultDistributionMethod ?? DistributionMethod::EQUAL)->value;
    }

    public function render(): View
    {
        return view('livewire.bill-form');
    }

    public function updatedFormattedNewAmount($newAmount): void
    {
        $this->newAmount = (int)round((float)$newAmount * 100);
        $this->formattedNewAmount = $this->formatCurrency($this->newAmount);
    }

    public function getDistributionMethodOptionsProperty(): array
    {
        return DistributionMethod::options();
    }

    public function getHouseholdMemberOptionsProperty(): array
    {
        return $this->householdMembers
            ->mapWithKeys(
                function (Member $member) {
                    return [$member->id => $member->full_name];
                }
            )->toArray();
    }
}
