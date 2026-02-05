<?php

namespace Tests\Unit\Models;

use App\Models\Expense;
use App\Models\ExpenseTab;
use App\Models\Household;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('expense has a member relationship', function () {
    $member = Member::factory()->create();
    $tab = ExpenseTab::factory()->create(['household_id' => $member->household_id]);
    $expense = Expense::factory()->create([
        'member_id' => $member->id,
        'expense_tab_id' => $tab->id
    ]);

    expect($expense->member)->toBeInstanceOf(Member::class)
        ->and($expense->member->id)->toBe($member->id);
});

test('expense has an expense tab relationship', function () {
    $tab = ExpenseTab::factory()->create(['household_id' => Household::factory()->create()->id]);
    $member = Member::factory()->create(['household_id' => $tab->household_id]);
    $expense = Expense::factory()->create([
        'expense_tab_id' => $tab->id,
        'member_id' => $member->id
    ]);

    expect($expense->expenseTab)->toBeInstanceOf(ExpenseTab::class)
        ->and($expense->expenseTab->id)->toBe($tab->id);
});
