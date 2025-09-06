<?php

namespace App\Livewire\Home;

use App\Domains\ValueObjects\Amount;
use App\Rules\ValidAmount;
use App\Services\Household\HouseholdServiceContract;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class AccountsList extends Component
{
    public Collection $members;
    public array $incomes = [];

    public array $incomesInCents = [];

    /**
     * @throws Exception
     */
    public function mount(HouseholdServiceContract $householdService): void
    {
        if (!$householdService->getCurrentHousehold()) {
            throw new Exception("No household exists");
        }

        $this->members = $householdService->getCurrentHousehold()->members;
    }

    public function render(): View
    {
        if ($this->members->count() === 0) {
            return view('livewire.home.accounts-list-empty');
        }

        $members = $this->members;

        return view('livewire.home.accounts-list', compact('members'));
    }

    public function updatedIncomes(string $amount, int $memberId): void
    {
        if (empty($amount)) {
            unset($this->incomes[$memberId]);
            unset($this->incomesInCents[$memberId]);

            $this->dispatch('incomeModified', memberId: $memberId, amount: null);
            return;
        }

        $this->validateOnly('incomes.' . $memberId);

        $amount = Amount::from($amount);

        $this->incomes[$memberId] = $amount->toCurrency();
        $this->incomesInCents[$memberId] = $amount->toCents();
        $this->dispatch('incomeModified', memberId: $memberId, amount: $amount->value());
    }

    #[Computed]
    public function totalIncomes(): ?Amount
    {
        if (count($this->incomesInCents) !== count($this->members)) {
            return null;
        }

        return new Amount(array_sum($this->incomesInCents));
    }

    public function ratioForMember(int $memberId): string
    {
        if (empty($this->incomesInCents[$memberId])) {
            return "-";
        }

        if (count($this->incomesInCents) !== count($this->members)) {
            return "-";
        }

        $ratio = $this->incomesInCents[$memberId] / array_sum($this->incomesInCents) * 100;
        $ratio = round($ratio, 2);

        return "$ratio%";
    }

    protected function rules(): array
    {
        return [
            'incomes.*' => [new ValidAmount()],
        ];
    }
}
