<?php

namespace Tests\Feature\Livewire\ExpenseTabs;

use App\Livewire\ExpenseTabs\ExpensesTable;
use App\Models\Expense;
use App\Models\ExpenseTab;
use Livewire\Livewire;

describe("Expense Tab sorting and pagination", function () {

    beforeEach(function () {
        $this->factory = test_factory()
            ->withHousehold()
            ->withMember(['first_name' => 'Huwey']);
    });

    test("should sort expenses by date descending", function () {
        $expenseTab = ExpenseTab::factory()->create([
            'household_id' => $this->factory->household()->id,
        ]);

        $oldest = Expense::factory()->create([
            'expense_tab_id' => $expenseTab->id,
            'name' => 'Oldest',
            'spent_on' => '2026-01-01',
            'member_id' => $this->factory->members()->first()->id,
        ]);

        $newest = Expense::factory()->create([
            'expense_tab_id' => $expenseTab->id,
            'name' => 'Newest',
            'spent_on' => '2026-02-01',
            'member_id' => $this->factory->members()->first()->id,
        ]);

        Livewire::test(ExpensesTable::class, ['expenseTabId' => $expenseTab->id])
            ->assertSeeInOrder(['Newest', 'Oldest']);
    });

    test("should paginate expenses by 15 items per page", function () {
        $expenseTab = ExpenseTab::factory()->create([
            'household_id' => $this->factory->household()->id,
        ]);

        // Create 16 expenses
        Expense::factory()->count(16)->create([
            'expense_tab_id' => $expenseTab->id,
            'member_id' => $this->factory->members()->first()->id,
        ]);

        $expenses = Expense::orderBy('spent_on', 'desc')->get();

        $test = Livewire::test(ExpensesTable::class, ['expenseTabId' => $expenseTab->id])
            ->assertSee($expenses[0]->name)
            ->assertSee($expenses[14]->name)
            ->assertDontSee($expenses[15]->name);
    });

    test("should highlight expenses not in current month", function () {
        $expenseTab = ExpenseTab::factory()->create([
            'household_id' => $this->factory->household()->id,
            'from_day' => 1,
        ]);

        // Current month expense (assuming current date is 2026-02-01)
        $currentMonthExpense = Expense::factory()->create([
            'expense_tab_id' => $expenseTab->id,
            'name' => 'Current Month',
            'spent_on' => '2026-02-01',
            'member_id' => $this->factory->members()->first()->id,
        ]);

        // Other month expense
        $otherMonthExpense = Expense::factory()->create([
            'expense_tab_id' => $expenseTab->id,
            'name' => 'Other Month',
            'spent_on' => '2026-01-15',
            'member_id' => $this->factory->members()->first()->id,
        ]);

        // Future month expense
        $futureMonthExpense = Expense::factory()->create([
            'expense_tab_id' => $expenseTab->id,
            'name' => 'Future Month',
            'spent_on' => '2026-03-01',
            'member_id' => $this->factory->members()->first()->id,
        ]);

        Livewire::test(ExpensesTable::class, ['expenseTabId' => $expenseTab->id])
            ->assertSeeHtml('class="opacity-50"') // Other Month
            ->assertSeeHtml('class="opacity-50"') // Future Month
            ->assertDontSeeHtml('class="opacity-50"<td>Current Month');
    });
});
