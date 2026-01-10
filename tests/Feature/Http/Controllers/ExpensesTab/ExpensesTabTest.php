<?php

namespace Tests\Feature\Http\Controllers\ExpensesTab;

use App\Models\Expense;
use App\Models\ExpenseTab;

beforeEach(function () {

    $context = test_factory()
        ->withHousehold()
        ->withMember()
        ->withUser();
    $this->household = $context->household();
    $this->user = $context->user();
});

it('can create an expanse tab', function () {
    $this->actingAs($this->user)
        ->post(route('expenses-tab.store'), [
            'name' => 'Courses',
            'period_start_day' => '5',
            'period_end_day' => '5',
            'household_id' => $this->household->id
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('expense_tabs', [
        'name' => 'Courses',
        'period_start_day' => 5,
        'period_end_day' => 5
    ]);
});

it('can create an expense linked to a tab', function () {
    $tab = ExpenseTab::factory()->create(['household_id' => $this->household->id]);

    Expense::factory()->create([
        'expense_tab_id' => $tab->id,
        'amount' => 5600,
        'member_id' => $this->user->member->id,
        'name' => 'Courses',
        'spent_at' => now()
    ]);

    $this->assertDatabaseHas('expenses', [
        'expense_tab_id' => $tab->id,
        'amount' => 5600,
        'name' => 'Courses'
    ]);
});
