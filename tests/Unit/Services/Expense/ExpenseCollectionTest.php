<?php

namespace Tests\Services\Expense;

use App\Domains\ValueObjects\Amount;
use App\Models\Expense;
use App\Models\ExpenseTab;
use App\Services\Expense\ExpensesCollection;

describe("ExpenseCollection", function () {

    beforeEach(function () {
        $this->factory = test_factory()
            ->withHousehold()
            ->withMember()
            ->withMember();


        $this->expenseTab = ExpenseTab::factory()->create(
            ['household_id' => $this->factory->household()->id]
        );
        $this->expenses = Expense::factory()->count(10)
            ->create([
                'amount' => new Amount(10000),
                'expense_tab_id' => $this->expenseTab->id,
                'member_id' => $this->factory->member()->id
            ]);
    });

    test('sum should return the total amount of expenses', function () {

        $expenseCollection = ExpensesCollection::from($this->expenses);
        expect($expenseCollection->getTotal())->toEqual(new Amount(100000));
    });
});
