<?php

namespace Tests\Feature\Livewire\ExpenseTabs;

use App\Livewire\ExpenseTabs\ExpensesTable;
use App\Models\Expense;
use App\Models\ExpenseTab;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

describe("ExpensesTable component", function () {

    beforeEach(function () {
        $this->factory = test_factory()
            ->withHousehold()
            ->withMember(['first_name' => 'Donald'])
            ->withUser();

        $this->expenseTab = ExpenseTab::factory()->create([
            'household_id' => $this->factory->household()->id,
        ]);

        $this->actingAs($this->factory->user());
    });

    test("it can set editing expense id", function () {
        $expense = Expense::factory()->create([
            'expense_tab_id' => $this->expenseTab->id,
            'member_id' => $this->factory->members()->first()->id,
        ]);

        Livewire::test(ExpensesTable::class, ['expenseTabId' => $this->expenseTab->id])
            ->call('editExpense', $expense->id)
            ->assertSet('editingExpenseId', $expense->id);
    });

    test("it resets editing expense id on update or cancel", function () {
        $expense = Expense::factory()->create([
            'expense_tab_id' => $this->expenseTab->id,
            'member_id' => $this->factory->members()->first()->id,
        ]);

        $component = Livewire::test(ExpensesTable::class, ['expenseTabId' => $this->expenseTab->id])
            ->set('editingExpenseId', $expense->id);

        $component->dispatch('expense-has-been-updated')
            ->assertSet('editingExpenseId', null);

        $component->set('editingExpenseId', $expense->id)
            ->dispatch('cancel-edit-expense')
            ->assertSet('editingExpenseId', null);
    });
});
