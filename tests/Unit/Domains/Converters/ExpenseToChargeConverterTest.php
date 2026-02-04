<?php

use App\Domains\Converters\ExpenseToChargeConverter;
use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Models\Expense;
use App\Models\ExpenseTab;
use App\Models\Household;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('ExpenseToChargeConverter', function () {
    test('it converts an expense to a charge', function () {
        // Given
        $household = Household::factory()->create();
        $member = Member::factory()->create([
            'first_name' => 'Donald',
            'last_name' => 'Duck',
            'household_id' => $household->id,
        ]);
        $expenseTab = ExpenseTab::factory()->create([
            'household_id' => $household->id,
        ]);
        $expense = Expense::factory()->create([
            'amount' => new Amount(1000),
            'distribution_method' => DistributionMethod::EQUAL,
            'member_id' => $member->id,
            'expense_tab_id' => $expenseTab->id,
        ]);

        $converter = new ExpenseToChargeConverter();

        // When
        $charge = $converter->convert($expense);

        // Then
        expect($charge->getAmountOrZero()->toCents())->toBe($expense->amount->toCents())
            ->and($charge->getDistributionMethod())->toBe($expense->distribution_method)
            ->and($charge->getPayer()->id)->toBe($expense->member_id);
    });
});
