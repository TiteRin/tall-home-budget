<?php

namespace App\Livewire;

use App\Actions\CreateBill;
use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Models\Member;
use Closure;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Prop;
use Livewire\Component;

class BillForm extends Component
{
    #[Prop]
    public DistributionMethod $defaultDistributionMethod;
    #[Prop]
    public Collection $householdMembers;
    #[Prop]
    public bool $hasJointAccount = true;

    public string $newName = '';
    public int $newAmount;
    public string $formattedNewAmount = "";
    public string $newDistributionMethod;
    public int|null $newMemberId = null;

    public function mount(): void
    {
        $this->newDistributionMethod = ($this->defaultDistributionMethod ?? DistributionMethod::EQUAL)->value;
        $this->householdMembers = $this->householdMembers ?? collect();
    }

    /**
     * @return void
     */
    public function resetFormFields(): void
    {
        $this->reset(['newName', 'formattedNewAmount', 'newMemberId']);
        $this->newAmount = 0;
        $this->newDistributionMethod = $this->defaultDistributionMethod->value;
        $this->resetValidation();
    }

    protected function rules(): array
    {
        return [
            'newName' => 'required|string|min:1',
            'newAmount' => [
                'required',
                'gt:0',
                function (string $attribute, string $value, Closure $fail) {
                    $amount = new Amount($value);
                    if ($amount->toCurrency() === $this->formattedNewAmount) return;

                    $fail("Le champ $attribute n'est pas valide.");
                }
            ],
            'formattedNewAmount' => 'required|string|min:1',
            'newDistributionMethod' => 'required|in:' . implode(",", DistributionMethod::values()),
            'newMemberId' => [
                'required',
                'integer',
                'in:' . implode(",", array_merge($this->householdMembers->pluck('id')->toArray(), [$this->hasJointAccount ? -1 : null])),
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
        if ($this->householdMembers->isEmpty()) {
            return view('livewire.bill-form-empty');
        }

        return view('livewire.bill-form');
    }

    public function addBill(CreateBill $createBill): void
    {
        $this->validate();

        try {

            $createBill->handle(
                $this->newName,
                new Amount($this->newAmount),
                DistributionMethod::from($this->newDistributionMethod),
                $this->newMemberId === -1 ? null : $this->newMemberId
            );

            $this->resetFormFields();

            // Dispatch events to refresh the bills table and show notification
            $this->dispatch('refreshBills');
//            $this->dispatch('notify', [
//                'message' => 'Dépense ajoutée avec succès',
//                'type' => 'success'
//            ]);
        } catch (Exception $e) {
            // Handle exception and show notification
//            $this->dispatch('notify', [
//                'message' => 'Une erreur est survenue: ' . $e->getMessage(),
//                'type' => 'error'
//            ]);
        }
    }

    public function updatedFormattedNewAmount(string $newAmount): void
    {
        if (!Amount::isValid($newAmount)) {

            $this->newAmount = $this->newAmount ?? 0;
            return;
        }

        $amount = Amount::from($newAmount);

        $this->newAmount = $amount->value();
        $this->formattedNewAmount = $amount->toCurrency();
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
