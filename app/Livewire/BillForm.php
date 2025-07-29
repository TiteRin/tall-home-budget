<?php

namespace App\Livewire;

use App\Enums\DistributionMethod;
use App\Models\Member;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

use App\Traits\HasCurrencyFormatting;

class BillForm extends Component
{

    use HasCurrencyFormatting;

    public DistributionMethod $defaultDistributionMethod;
    public Collection $householdMembers;
    public bool $hasJointAccount = true;


    #[Validate('required', as: "Nouvelle dépense", message: 'Le champ "Nouvelle dépense" est requis.')]
    #[Validate('string|min:1', as: "Nouvelle dépense", message: 'La valeur du champ "Nouvelle dépense" est trop courte.')]
    public string $newName = '';

    #[Validate('required', as: "Montant", message: 'Le montant est requis.')]
    #[Validate('gt:0', as: "Montant", message: "Le montant doit être supérieur à zéro.")]
    public int $newAmount;
    #[Validate('required|string|min:1')]
    public string $formattedNewAmount;
    #[Validate('required|string|min:1')]
    public string $newDistributionMethod;
    #[Validate('nullable|exists:members,id')]
    public int|null $newMemberId;

    public function mount(): void
    {
        $this->newDistributionMethod = ($this->defaultDistributionMethod ?? DistributionMethod::EQUAL)->value;
        $this->householdMembers = $this->householdMembers ?? collect();
    }

    public function render(): View
    {
        return view('livewire.bill-form');
    }

    public function submit(): void
    {
        $this->validate();
    }

    public function updatedFormattedNewAmount(string $newAmount): void
    {
        if (!is_numeric($newAmount)) {
            $this->newAmount = -1;
            return;
        }
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
