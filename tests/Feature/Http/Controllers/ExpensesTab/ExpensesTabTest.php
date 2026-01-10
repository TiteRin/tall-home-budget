<?php

namespace Tests\Feature\Http\Controllers\ExpensesTab;

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
