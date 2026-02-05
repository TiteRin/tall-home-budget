<?php

namespace Tests\Feature\Livewire\ExpenseTabs;

use App\Livewire\ExpenseTabs\ExpensesTable;
use App\Models\Expense;
use App\Models\ExpenseTab;
use Livewire\Livewire;

describe("ExpensesTable Reactive Pagination", function () {

    beforeEach(function () {
        $this->factory = test_factory()
            ->withHousehold()
            ->withMember(['first_name' => 'Donald']);
    });

    test("la pagination ne doit pas être présente dans l'URL", function () {
        $expenseTab = ExpenseTab::factory()->create([
            'household_id' => $this->factory->household()->id,
        ]);

        Expense::factory()->count(31)->create([
            'expense_tab_id' => $expenseTab->id,
            'member_id' => $this->factory->members()->first()->id,
        ]);

        $component = Livewire::test(ExpensesTable::class, ['expenseTabId' => $expenseTab->id])
            ->call('gotoPage', 2);

        // Dans Livewire 3 avec WithPagination, la page est stockée dans $paginators['page']
        $this->assertEquals(2, $component->instance()->paginators['page']);
    });
});
