<?php

namespace App\Livewire\Home;

use App\Domains\ValueObjects\Amount;
use App\Rules\ValidAmount;
use Exception;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;

class AccountsList extends Component
{
    public array $members = [];
    public array $incomes = [];

    public array $incomesInCents = [];

    /**
     * @throws Exception
     */
    public function mount(?array $members = []): void
    {
        $this->members = $members;
    }

    public function render(): View
    {
        if (count($this->members) === 0) {
            return view('livewire.home.accounts-list-empty');
        }

        $members = $this->members;

        return view('livewire.home.accounts-list', compact('members'));
    }

    public function updatedIncomes(mixed $amount, int $memberId): void
    {
        $this->saveIncome($amount, $memberId);
    }

    public function initIncomes(array $incomes): void
    {
        foreach ($incomes as $memberId => $amount) {
            $this->saveIncome($amount, (int)$memberId);
        }
    }

    /**
     * @throws Exception
     */
    private function saveIncome(mixed $amount, int $memberId): void
    {
        if (empty($amount)) {
            unset($this->incomes[$memberId]);
            unset($this->incomesInCents[$memberId]);

            $this->dispatch('incomeModified', memberId: $memberId, amount: null);
            return;
        }

        $this->incomes[$memberId] = $amount;

        try {
            $this->validateOnly('incomes.' . $memberId);
        } catch (Exception $e) {
            unset($this->incomes[$memberId]);
            throw $e;
        }

        $amountVo = Amount::from($amount);

        $this->incomes[$memberId] = $amountVo->toCurrency();
        $this->incomesInCents[$memberId] = $amountVo->toCents();
        $this->dispatch('incomeModified', memberId: $memberId, amount: $amountVo->value());
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
