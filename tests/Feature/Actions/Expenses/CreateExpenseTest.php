<?php

namespace Tests\Feature\Actions\Expenses;

use App\Actions\Expenses\CreateExpense;
use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Exceptions\Households\MismatchedHouseholdException;
use App\Models\ExpenseTab;
use App\Models\Household;
use App\Services\Household\CurrentHouseholdService;
use Carbon\CarbonImmutable;

describe("Création de dépenses", function () {

    beforeEach(function () {

        $this->factory = test_factory()
            ->withHousehold()
            ->withMember()
            ->withUser();

        $this->expenseTab = ExpenseTab::factory()
            ->create([
                'household_id' => $this->factory->household()->id
            ]);

        $this->actingAs($this->factory->user());
    });

    test("should create a valid expense", function () {
        $service = new CurrentHouseholdService();
        $createExpense = new CreateExpense($service);

        $date = CarbonImmutable::now();

        $createExpense->handle(
            $this->factory->member()->id,
            DistributionMethod::EQUAL,
            $this->expenseTab->id,
            'Test',
            $date,
            Amount::from("100,00€")
        );

        $this->assertDatabaseHas('expenses', [
            'member_id' => $this->factory->member()->id,
            'expense_tab_id' => $this->expenseTab->id,
            'amount' => '10000',
            'name' => 'Test',
            'spent_on' => $date->format('Y-m-d')
        ]);
    });

    test("when member's household and expense tab’s household mismatch, should throw an exception", function () {
        $service = new CurrentHouseholdService();
        $createExpense = new CreateExpense($service);

        $expenseTabB = ExpenseTab::factory()->create([
            'household_id' => Household::factory()
        ]);

        $date = CarbonImmutable::now();

        $createExpense->handle(
            $this->factory->member()->id,
            DistributionMethod::EQUAL,
            $expenseTabB->id,
            'Test',
            $date,
            Amount::from("100,00€")
        );
    })->throws(MismatchedHouseholdException::class);
});
