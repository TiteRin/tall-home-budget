<?php

namespace Tests\Feature\Livewire\ExpenseTabs;

use App\Livewire\ExpenseTabs\ExpenseTabsList;
use App\Models\ExpenseTab;
use Livewire\Livewire;

describe("Expense Tabs List URL and Pagination", function () {

    beforeEach(function () {
        $this->factory = test_factory()
            ->withHousehold(['name' => 'Test household'])
            ->withMember(['first_name' => 'John'])
            ->withUser();
        $this->user = $this->factory->user();
        $this->actingAs($this->user);
    });

    test("it can set the active tab from the URL", function () {
        $tab = ExpenseTab::factory()->create([
            'household_id' => $this->user->member->household_id,
            'name' => 'Vacances',
            'from_day' => '1'
        ]);

        Livewire::withQueryParams(['tab' => $tab->id])
            ->test(ExpenseTabsList::class)
            ->assertSet('activeTab', $tab->id);
    });

    test("it defaults to 'new' if no tab is provided in the URL", function () {
        Livewire::test(ExpenseTabsList::class)
            ->assertSet('activeTab', 'new');
    });
});
