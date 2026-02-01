<?php

namespace Tests\Feature\Livewire\ExpenseTabs;

use App\Enums\DistributionMethod;
use App\Livewire\ExpenseTabs\ExpensesTable;
use App\Models\Expense;
use App\Models\ExpenseTab;
use Livewire\Livewire;

describe("Expense Tab shows a list of expenses", function () {

    beforeEach(function () {

        $this->factory = test_factory()
            ->withHousehold()
            ->withMember(['first_name' => 'Huwey'])
            ->withMember(['first_name' => 'Dewey'])
            ->withMember(['first_name' => 'Louis']);
    });

    test("should show a table of expenses", function () {
        $expenseTab = ExpenseTab::factory()->create([
            'name' => 'Test Expense Tab',
            'household_id' => $this->factory->household()->id,
            'from_day' => 5
        ]);

        for ($i = 0; $i < 10; $i++) {
            Expense::factory()->create([
                'expense_tab_id' => $expenseTab->id,
                'amount' => rand(100, 10000),
                'member_id' => $this->factory->members()->random()->id,
                'name' => 'Expense ' . $i,
                'spent_on' => now()->subDays($i)
            ]);
        }

        Livewire::test(ExpensesTable::class, ['expenseTabId' => $expenseTab->id])
            ->assertSeeText('Expense 0')
            ->assertSeeText('Expense 1')
            ->assertSeeText('Expense 2')
            ->assertSeeText('Expense 3')
            ->assertSeeText('Expense 4')
            ->assertSeeText('Expense 5')
            ->assertSeeText('Expense 6')
            ->assertSeeText('Expense 7')
            ->assertSeeText('Expense 8')
            ->assertSeeText('Expense 9');
    });

    test("should show date in DD/MM/YYYY format and distribution method with capital letter", function () {
        $expenseTab = ExpenseTab::factory()->create([
            'household_id' => $this->factory->household()->id,
        ]);

        $expense = Expense::factory()->create([
            'expense_tab_id' => $expenseTab->id,
            'name' => 'Pizza Night',
            'spent_on' => '2026-02-01',
            'distribution_method' => DistributionMethod::EQUAL,
            'member_id' => $this->factory->members()->first()->id,
        ]);

        Livewire::test(ExpensesTable::class, ['expenseTabId' => $expenseTab->id])
            ->assertSee('01/02/2026')
            ->assertSee(DistributionMethod::EQUAL->label());
    });
});
