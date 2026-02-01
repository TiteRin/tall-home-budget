<?php

namespace App\Livewire\ExpenseTabs;

use App\Actions\Expenses\CreateExpense;
use App\Actions\Expenses\UpdateExpense;
use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Models\Expense;
use App\Models\Member;
use Carbon\CarbonImmutable;
use Closure;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Prop;
use Livewire\Component;

class ExpenseForm extends Component
{
    #[Prop]
    public ?DistributionMethod $defaultDistributionMethod = null;
    #[Prop]
    public Collection $householdMembers;
    #[Prop]
    public int $expenseTabId;
    #[Prop]
    public ?Expense $expense = null;

    public string $newName = '';
    public int $newAmount = 0;
    public string $formattedNewAmount = "";
    public string $newDistributionMethod;
    public int|null $newMemberId = null;
    public string $newSpentOn = '';

    /**
     * @throws Exception
     */
    public function mount(): void
    {
        $this->newDistributionMethod = ($this->defaultDistributionMethod ?? DistributionMethod::EQUAL)->value;
        $this->householdMembers = $this->householdMembers ?? collect();
        $this->newSpentOn = now()->format('Y-m-d');

        if ($this->expense) {
            $this->newName = $this->expense->name;
            $this->newAmount = $this->expense->amount->value();
            $this->formattedNewAmount = $this->expense->amount->toCurrency();
            $this->newDistributionMethod = $this->expense->distribution_method->value;
            $this->newMemberId = $this->expense->member_id;
            $this->newSpentOn = $this->expense->spent_on->format('Y-m-d');
        }
    }

    public function resetFormFields(): void
    {
        $this->reset(['newName', 'formattedNewAmount', 'newMemberId']);
        $this->newAmount = 0;
        $this->newDistributionMethod = ($this->defaultDistributionMethod ?? DistributionMethod::EQUAL)->value;
        $this->newSpentOn = now()->format('Y-m-d');
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
                'in:' . implode(",", $this->householdMembers->pluck('id')->toArray()),
            ],
            'newSpentOn' => 'required|date',
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
            'newSpentOn.required' => 'Le champ ":attribute" est requis.',
            'newSpentOn.date' => 'Le champ ":attribute" doit être une date valide.',
        ];
    }

    protected function validationAttributes(): array
    {
        return [
            'newName' => 'Dépense',
            'newAmount' => 'Montant',
            'newDistributionMethod' => 'Méthode de distribution',
            'newMemberId' => 'Membre du foyer',
            'newSpentOn' => 'Date',
        ];
    }

    public function render(): View
    {
        if ($this->householdMembers->isEmpty()) {
            return view('livewire.expense-tabs.expenses-empty');
        }

        return view('livewire.expense-tabs.expense-form');
    }

    public function addExpense(CreateExpense $createExpense): void
    {
        $this->validate();

        try {
            $createExpense->handle(
                $this->newMemberId,
                DistributionMethod::from($this->newDistributionMethod),
                $this->expenseTabId,
                $this->newName,
                CarbonImmutable::parse($this->newSpentOn),
                new Amount($this->newAmount)
            );

            $this->resetFormFields();

            $this->dispatch('refresh-expenses-table');
            $this->dispatch('notify', type: 'success', message: 'Dépense ajoutée avec succès');
        } catch (Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Une erreur est survenue.', details: $e->getMessage());
        }
    }

    public function saveExpense(UpdateExpense $updateExpense): void
    {
        $this->validate();

        try {
            $updateExpense->handle(
                $this->expense->id,
                [
                    'name' => $this->newName,
                    'amount' => new Amount($this->newAmount),
                    'distribution_method' => DistributionMethod::from($this->newDistributionMethod),
                    'member_id' => $this->newMemberId,
                    'spent_on' => CarbonImmutable::parse($this->newSpentOn),
                    'expense_tab_id' => $this->expenseTabId,
                ]
            );
            $this->dispatch('expense-has-been-updated');
            $this->dispatch('refresh-expenses-table');
            $this->dispatch('notify', type: 'success', message: 'Dépense mise à jour avec succès');
        } catch (Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Une erreur est survenue.', details: $e->getMessage());
        }
    }

    public function cancelEdition(): void
    {
        $this->dispatch('cancel-edit-expense');
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
