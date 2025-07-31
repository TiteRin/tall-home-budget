<?php

namespace App\Livewire;

use App\Enums\DistributionMethod;
use App\Models\Member;
use App\Traits\HasCurrencyFormatting;
use Closure;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Component;

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
        $this->householdMembers = $this->householdMembers ?? collect();
    }

    protected function rules(): array
    {
        return [
            'newName' => 'required|string|min:1',
            'newAmount' => [
                'required',
                'gt:0',
                function (string $attribute, string $value, Closure $fail) {
                    if ($this->formatCurrency($value) === $this->formattedNewAmount) return;
                    $fail("Le champ $attribute n'est pas valide.");
                }
            ],
            'formattedNewAmount' => 'required|string|min:1',
            'newDistributionMethod' => 'required|in:' . implode(",", DistributionMethod::labels()),
            'newMemberId' => [
                $this->hasJointAccount ? 'nullable' : 'required',
                'integer',
                'in' . implode(",", $this->householdMembers->pluck('id')->toArray()),

            ]
        ];
    }

    protected function messages(): array
    {
        return [
            'newName.required' => 'Le champ ":attribute" est requis.',
            'newName.min' => 'Le champ ":attribute" ne peut pas être vide',
            'newAmount.required' => 'Le champ ":attribute" est requis.',
            'newAmount.gt' => 'Le champ ":attribute" doit être supérieur à zéro.',
            'newDistributionMethod.required' => 'Le champ ":attribute" est requis.',
            'newDistributionMethod.in' => 'Le champ ":attribute" n\'est pas valide.',
            'newMemberId.in' => 'Le champ ":attribute" n\'est pas valide.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'newName' => 'Nouvelle dépense',
            'newAmount' => 'Montant',
            'newDistributionMethod' => 'Méthode de distribution',
            'newMemberId' => 'Membre du foyer'
        ];
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
