<?php

use App\Domains\ValueObjects\Amount;
use App\Domains\ValueObjects\MonthlyPeriod;
use App\Enums\DistributionMethod;
use App\Models\Expense;
use App\Models\ExpenseTab;
use App\Models\Household;
use App\Models\Member;
use App\Services\Expense\ExpensesCollection;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('ExpensesCollection', function () {
    beforeEach(function () {
        $this->household = Household::factory()->create();
        $this->member = Member::factory()->create(['household_id' => $this->household->id]);
        $this->expenseTab = ExpenseTab::factory()->create(['household_id' => $this->household->id]);

        $this->createExpense = function (int $cents, $date = '2025-01-01', $method = DistributionMethod::EQUAL, $member = null) {
            return Expense::factory()->create([
                'amount' => new Amount($cents),
                'spent_on' => CarbonImmutable::parse($date),
                'distribution_method' => $method,
                'member_id' => $member?->id ?? $this->member->id,
                'expense_tab_id' => $this->expenseTab->id,
            ]);
        };
    });

    test('it calculates total amount', function () {
        $expenses = new ExpensesCollection([
            ($this->createExpense)(1000),
            ($this->createExpense)(2000),
        ]);

        expect($expenses->getTotal()->toCents())->toBe(3000);
    });

    test('it filters by monthly period', function () {
        $period = new MonthlyPeriod(
            CarbonImmutable::create(2025, 1, 1),
            CarbonImmutable::create(2025, 1, 31)
        );

        $expenses = new ExpensesCollection([
            ($this->createExpense)(1000, '2025-01-15'),
            ($this->createExpense)(2000, '2025-02-01'),
        ]);

        $filtered = $expenses->forMonthlyPeriod($period);

        expect($filtered)->toHaveCount(1)
            ->and($filtered->first()->amount->toCents())->toBe(1000);

        // Test with null period (should return all)
        $all = $expenses->forMonthlyPeriod(null);
        expect($all)->toHaveCount(2);
    });

    test('it calculates total for distribution method', function () {
        $expenses = new ExpensesCollection([
            ($this->createExpense)(1000, '2025-01-01', DistributionMethod::EQUAL),
            ($this->createExpense)(2000, '2025-01-01', DistributionMethod::PRORATA),
        ]);

        expect($expenses->getTotalForDistributionMethod(DistributionMethod::EQUAL)->toCents())->toBe(1000)
            ->and($expenses->getTotalForDistributionMethod(DistributionMethod::PRORATA)->toCents())->toBe(2000);
    });

    test('it calculates total for member', function () {
        $member2 = Member::factory()->create(['household_id' => $this->household->id]);

        $expenses = new ExpensesCollection([
            ($this->createExpense)(1000, '2025-01-01', DistributionMethod::EQUAL, $this->member),
            ($this->createExpense)(2000, '2025-01-01', DistributionMethod::EQUAL, $member2),
        ]);

        expect($expenses->getTotalForMember($this->member)->toCents())->toBe(1000)
            ->and($expenses->getTotalForMember($member2)->toCents())->toBe(2000);
    });

    test('it calculates total for joint account', function () {
        $expenses = new ExpensesCollection([]);
        expect($expenses->getTotalForJointAccount()->toCents())->toBe(0);
    });
});
