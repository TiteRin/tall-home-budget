<?php

namespace Tests\Unit\Actions\Expenses;

use App\Actions\Expenses\DeleteExpense;
use App\Exceptions\Households\MismatchedHouseholdException;
use App\Models\Expense;
use App\Models\ExpenseTab;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe("DeleteExpense action", function () {

    beforeEach(function () {
        $this->factory = test_factory()
            ->withHousehold()
            ->withMember(['first_name' => 'Huey'])
            ->withUser();

        $this->expenseTab = ExpenseTab::factory()->create([
            'household_id' => $this->factory->household()->id,
        ]);

        $this->actingAs($this->factory->user());
    });

    test("it can delete an expense from the same household", function () {
        $expense = Expense::factory()->create([
            'expense_tab_id' => $this->expenseTab->id,
            'member_id' => $this->factory->members()->first()->id,
        ]);

        $action = app(DeleteExpense::class);
        $action->handle($expense->id);

        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    });

    test("it cannot delete an expense from another household", function () {
        $otherHousehold = test_factory()
            ->withHousehold()
            ->withMember(['first_name' => 'Donald'])
            ->household();

        $otherExpenseTab = ExpenseTab::factory()->create([
            'household_id' => $otherHousehold->id,
        ]);

        $otherExpense = Expense::factory()->create([
            'expense_tab_id' => $otherExpenseTab->id,
            'member_id' => $otherHousehold->members()->first()->id,
        ]);

        $action = app(DeleteExpense::class);

        expect(fn() => $action->handle($otherExpense->id))
            ->toThrow(MismatchedHouseholdException::class);

        $this->assertDatabaseHas('expenses', ['id' => $otherExpense->id]);
    });
});
