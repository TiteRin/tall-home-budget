<?php

namespace App\Livewire;

use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Models\Member;
use Closure;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
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
                $this->hasJointAccount ? 'nullable' : 'required',
                'integer',
                'in:' . implode(",", $this->householdMembers->pluck('id')->toArray()),
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

        try {
            // Use HTTP client to call the controller endpoint
            $response = Http::post(route('bills.store'), [
                'name' => $this->newName,
                'amount' => $this->newAmount,
                'distribution_method' => $this->newDistributionMethod,
                'member_id' => $this->newMemberId,
            ]);

            if ($response->successful()) {
                // Reset form fields after successful submission
                $this->reset(['newName', 'formattedNewAmount']);
                $this->newAmount = 0;
                $this->resetValidation();

                // Dispatch events to refresh the bills table and show notification
                $this->dispatch('refreshBills');
                $this->dispatch('notify', [
                    'message' => 'Dépense ajoutée avec succès',
                    'type' => 'success'
                ]);
            } else {
                // Handle error response and show notification
                $errorMessage = $response->json('message', 'Une erreur inconnue est survenue');
                $this->dispatch('notify', [
                    'message' => 'Échec de création de la dépense: ' . $errorMessage,
                    'type' => 'error'
                ]);
            }
        } catch (Exception $e) {
            // Handle exception and show notification
            $this->dispatch('notify', [
                'message' => 'Une erreur est survenue: ' . $e->getMessage(),
                'type' => 'error'
            ]);
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
