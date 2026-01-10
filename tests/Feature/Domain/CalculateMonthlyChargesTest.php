<?php

namespace Tests\Feature\Domain;

use App\Domains\Charges\ChargeCalculator;
use App\Enums\DistributionMethod;
use App\Models\Bill;
use App\Models\Expense;
use App\Models\ExpenseTab;

beforeEach(function () {

    $context = test_factory()
        ->withHousehold(['name' => 'Duck Household'])
        ->withMember(['first_name' => 'Dewey', 'last_name' => 'Duck'])
        ->withUser()
        ->withMember(['first_name' => 'Huwey', 'last_name' => 'Duck'])
        ->withMember(['first_name' => 'Louis', 'last_name' => 'Duck']);

    $this->household = $context->household();
    $this->user = $context->user();
    $this->actingAs($context->user());
});

it('includes monthly expenses in shared charges', function () {

    $billLoyer = Bill::factory()->create([
        'name' => 'Loyer',
        'amount' => 100000,
        'distribution_method' => DistributionMethod::EQUAL,
        'household_id' => $this->household->id,
        'member_id' => $this->user->member->id
    ]);

    $tabExpense = ExpenseTab::factory()->create([
        'household_id' => $this->household->id,
        'period_start_day' => 5,
        'period_end_day' => 5
    ]);

    Expense::factory()->create([
        'name' => 'Courses',
        'expense_tab_id' => $tabExpense->id,
        'amount' => 10000,
        'spent_at' => '2026-01-05',
        'member_id' => $this->user->member->id
    ]);

    Expense::factory()->create([
        'name' => 'Courses',
        'expense_tab_id' => $tabExpense->id,
        'amount' => 20000,
        'spent_at' => '2026-01-10',
        'member_id' => $this->user->member->id
    ]);

    // Expense NOT IN THE MONTH


    Expense::factory()->create([
        'name' => 'Courses',
        'expense_tab_id' => $tabExpense->id,
        'amount' => 20000,
        'spent_at' => '2026-01-03',
        'member_id' => $this->user->member->id
    ]);

    $charges = ChargeCalculator::forHousehold($this->household)
        ->forMonth('2026-01')
        ->calculate();

    expect($charges->expensesTotal()->toCents())->toBe(30000);
    expect($charges->billsTotal()->toCents())->toBe(100000);
    expect($charges->total()->toCents())->toBe(130000);
});
