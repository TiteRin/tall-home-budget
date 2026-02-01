<?php

namespace Tests\Feature\Expenses\ExpenseTabs;

use App\Models\ExpenseTab;
use App\Models\Household;

describe("Expense Tab Authorization", function () {

    beforeEach(function () {

        $this->factory = test_factory()
            ->withHousehold(['name' => 'Test household'])
            ->withMember(['first_name' => 'John'])
            ->withUser();
        $this->user = $this->factory->user();
        $this->actingAs($this->user);
    });

    test("given a member, they should see their expense tabs", function () {
        $expenseTabA = ExpenseTab::factory()->create([
            'household_id' => $this->user->member->household_id,
            'name' => 'Cats budget',
            'from_day' => '5'
        ]);
        $expenseTabB = ExpenseTab::factory()->create([
            'household_id' => $this->user->member->household_id,
            'name' => 'Groceries',
            'from_day' => '1'
        ]);

        $this->get(route('expense-tabs.index'))
            ->assertSeeText('Cats budget')
            ->assertSeeText('Groceries');
    });

    test("given a member, they shouldn’t see expense tabs from another household", function () {
        $expenseTabA = ExpenseTab::factory()->create([
            'household_id' => $this->user->member->household_id,
            'name' => 'Cats budget',
            'from_day' => '5'
        ]);
        $expenseTabB = ExpenseTab::factory()->create([
            'household_id' => Household::factory()->create()->id,
            'name' => 'Groceries',
            'from_day' => '1'
        ]);

        $this->get(route('expense-tabs.index'))
            ->assertSeeText('Cats budget')
            ->assertDontSeeText('Groceries');
    });

    test('given a member, they should create an expense tab for their household', function () {

    });

    test('given a member, they should not create an expense tab for another household', function () {

    });
});
