<?php

namespace Tests\Feature\Livewire\ExpenseTabs;

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
});
