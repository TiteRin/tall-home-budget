<?php

namespace Tests\Feature\Livewire\ExpenseTabs;

use App\Livewire\ExpenseTabs\ExpenseTabsList;
use App\Models\ExpenseTab;
use Livewire\Livewire;

describe("Expense Tabs List", function () {

    beforeEach(function () {
        $this->factory = test_factory()
            ->withHousehold(['name' => 'Test household'])
            ->withMember(['first_name' => 'John'])
            ->withUser();
        $this->user = $this->factory->user();
        $this->actingAs($this->user);
    });

    test("when no tabs are existing, should only see 'Nouvel onglet de dépense'", function () {
        $this->get(route('expense-tabs.index'))
            ->assertSeeText('Nouvel onglet de dépense');
    });

    test("when an event is dispatched, should refresh the list", function () {
        $component = Livewire::test(ExpenseTabsList::class)
            ->assertDontSeeText('Test Tab');

        $tab = ExpenseTab::factory()->create([
            'household_id' => $this->user->member->household_id,
            'name' => 'Test Tab',
            'from_day' => '5'
        ]);

        $component
            ->dispatch('refresh-expense-tabs', $tab)
            ->assertSeeText('Test Tab');
    });
});
