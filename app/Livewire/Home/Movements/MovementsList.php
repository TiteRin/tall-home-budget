<?php

namespace App\Livewire\Home\Movements;

use App\Domains\Converters\BillToChargeConverter;
use App\Domains\Converters\ChargesAssembler;
use App\Domains\Converters\ExpenseToChargeConverter;
use App\Services\Bill\BillsCollection;
use App\Services\Expense\ExpensesCollection;
use App\Services\Expense\ExpenseServiceResolver;
use App\Services\Household\CurrentHouseholdServiceContract;
use App\Services\Movement\MovementsService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Attributes\Reactive;
use Livewire\Component;

class MovementsList extends Component
{
    #[Reactive]
    public array $incomes = [];

    private function buildService(): MovementsService
    {
        $currentHousehold = app(CurrentHouseholdServiceContract::class)->getCurrentHousehold();
        $chargesAssembler = new ChargesAssembler(
            new BillToChargeConverter(),
            new ExpenseToChargeConverter()
        );

        $expensesCollection = new ExpensesCollection();

        foreach ($currentHousehold->expenseTabs() as $expenseTab) {
            $expenseTabResolver = new ExpenseServiceResolver($expenseTab->from_day);
            $monthlyPeriod = $expenseTabResolver->getCurrentMonthlyPeriod();

            $expensesCollection->push(
                $expenseTab->expenses()->whereBetween('spent_on', [
                    $monthlyPeriod->start,
                    $monthlyPeriod->end
                ])
            );
        }

        $chargesAssembler
            ->fromBills(new BillsCollection($currentHousehold->bills))
            ->fromExpenses(new ExpensesCollection($expensesCollection));


        $service = MovementsService::create()
            ->withMembers($currentHousehold->members)
            ->withCharges($chargesAssembler->assemble())
            ->withBills(new BillsCollection($currentHousehold->bills))
            ->withExpenses($expensesCollection);

        $service = $service->withIncomes($this->incomes);

        return $service;
    }

    public function getMovementsProperty(): Collection
    {
        return $this->buildService()->toMovements();
    }

    public function render(): View
    {
        if ($this->movements->isEmpty()) {
            return view('livewire.home.movements.movements-list-empty');
        }

        return view('livewire.home.movements.movements-list', [
            'movements' => $this->movements,
        ]);
    }
}
