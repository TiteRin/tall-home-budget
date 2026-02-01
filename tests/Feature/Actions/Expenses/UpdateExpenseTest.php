<?php

namespace Tests\Feature\Actions\Expenses;

use App\Actions\Expenses\UpdateExpense;
use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Exceptions\Households\MismatchedHouseholdException;
use App\Models\Expense;
use App\Models\ExpenseTab;
use App\Models\Household;
use App\Models\Member;
use App\Services\Household\CurrentHouseholdService;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\ModelNotFoundException;

describe("Mise à jour de dépenses", function () {

    beforeEach(function () {

        $this->factory = test_factory()
            ->withHousehold()
            ->withMember()
            ->withUser();

        $this->expenseTab = ExpenseTab::factory()
            ->create([
                'household_id' => $this->factory->household()->id
            ]);

        $this->expense = Expense::factory()->create([
            'expense_tab_id' => $this->expenseTab->id,
            'member_id' => $this->factory->member()->id,
            'distribution_method' => DistributionMethod::EQUAL,
            'name' => 'Initial Expense',
            'amount' => Amount::from("100,00€"),
            'spent_on' => CarbonImmutable::now(),
        ]);

        $this->actingAs($this->factory->user());
    });

    test("should update a valid expense", function () {
        $service = new CurrentHouseholdService();
        $updateExpense = new UpdateExpense($service);

        $date = CarbonImmutable::now()->subDay();

        $updateExpense->handle(
            $this->expense->id,
            [
                'name' => 'New Name',
                'amount' => Amount::from("200,00€"),
                'spent_on' => $date,
            ]
        );

        $this->assertDatabaseHas('expenses', [
            'id' => $this->expense->id,
            'name' => 'New Name',
            'amount' => '20000',
            'spent_on' => $date->format('Y-m-d')
        ]);
    });

    test("should not be possible to update an expense that does not exist", function () {
        $service = new CurrentHouseholdService();
        $updateExpense = new UpdateExpense($service);

        $date = CarbonImmutable::now()->subDay();

        $updateExpense->handle(
            999,
            [
                'name' => 'New Name',
                'amount' => Amount::from("200,00€"),
                'spent_on' => $date,
            ]
        );
    })->throws(ModelNotFoundException::class);

    test("should not be possible to move to another household’s expense tab", function () {
        $service = new CurrentHouseholdService();
        $updateExpense = new UpdateExpense($service);

        $date = CarbonImmutable::now()->subDay();

        $expenseTabB = ExpenseTab::factory()->create([
            'household_id' => Household::factory()
        ]);

        $updateExpense->handle(
            $this->expense->id,
            [
                'expense_tab_id' => $expenseTabB->id
            ]
        );
    })->throws(MismatchedHouseholdException::class);

    test("should not be possible to move to another household’s member", function () {
        $service = new CurrentHouseholdService();
        $updateExpense = new UpdateExpense($service);

        $date = CarbonImmutable::now()->subDay();

        $memberB = Member::factory()->create([
            'household_id' => Household::factory()
        ]);

        $updateExpense->handle(
            $this->expense->id,
            [
                'member_id' => $memberB->id
            ]
        );
    })->throws(MismatchedHouseholdException::class);
});
