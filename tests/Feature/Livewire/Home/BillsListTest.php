<?php

namespace Tests\Feature\Livewire\Home;

use App\Domains\ValueObjects\Amount;
use App\Livewire\Bills\BillsList;
use App\Models\Expense;
use App\Models\ExpenseTab;
use App\Services\Expense\ExpenseCollection;
use Livewire;

test('should display the component', function () {
    Livewire::test(BillsList::class)
        ->assertOk();
});

describe("no bills are passed", function () {
    test('should display a message', function () {
        Livewire::test(BillsList::class)
            ->assertSee("Aucune charge");
    });

    test('should display a link to the bills manager', function () {
        Livewire::test(BillsList::class)
            ->assertSee("Paramétrer les charges")
            ->assertSeeHtml('href="' . route('bills.settings') . '"');
    });
});

describe('When the component has bills', function () {

    beforeEach(function () {
        $this->household = bill_factory()->household();
        $this->memberJohn = bill_factory()->member(['first_name' => 'John'], $this->household);
        $this->memberMarie = bill_factory()->member(['first_name' => 'Marie'], $this->household);

        $this->billRent = bill_factory()->bill([
            'name' => 'Loyer',
            'amount' => 67000,
        ], null, $this->household);
        $this->billInternet = bill_factory()->bill([
            'name' => 'Internet',
            'amount' => 2999,
        ], $this->memberJohn, $this->household);
        $this->billEnergy = bill_factory()->bill([
            'name' => 'Électricité',
            'amount' => 12100,
        ], $this->memberJohn, $this->household);
        $this->billPhones = bill_factory()->bill([
            'name' => 'Abonnements téléphones',
            'amount' => 2498,
        ], $this->memberMarie, $this->household);
        $this->billWater = bill_factory()->bill([
            'name' => 'Eau',
            'amount' => 2500,
        ], $this->memberMarie, $this->household);

        $this->props = ['bills' => [
            $this->billRent,
            $this->billInternet,
            $this->billEnergy,
            $this->billPhones,
            $this->billWater,
        ]];
    });

    test('should display a table', function () {
        Livewire::test(BillsList::class, $this->props)
            ->assertSeeHtml('<table');
    });

    test('should display the bills', function () {
        Livewire::test(BillsList::class, $this->props)
            ->assertSeeInOrder(
                [
                    'Loyer',
                    'Internet',
                    'Électricité',
                    'Abonnements téléphones',
                    'Eau'
                ]
            );
    });

    test('should display the total', function () {
        $bills = bill_factory()->bills(5, ['amount' => 10000], null, $this->household);
        Livewire::test(BillsList::class, ['bills' => $bills->all()])
            ->assertSee('500,00 €');
    });
});


describe("When the household has expenses", function () {

    beforeEach(function () {
        $this->factory = test_factory()
            ->withHousehold(['name' => 'Duck'])
            ->withMember(['first_name' => 'Daisy'])
            ->withMember(['first_name' => 'Donald'])
            ->withUser();

        $this->actingAs($this->factory->user());

        $this->expenseTabGroceries = ExpenseTab::factory()->create([
            'household_id' => $this->factory->household()->id,
            'name' => 'Groceries',
            'from_day' => 5
        ]);

        $this->expenses = Expense::factory()->count(10)
            ->create([
                'expense_tab_id' => $this->expenseTabGroceries->id,
                'member_id' => $this->factory->members()->random()->id,
                'spent_on' => now()->subDays(random_int(0, 10)),
                'amount' => new Amount(1000)
            ]);

        $this->totalAmount = ExpenseCollection::from($this->expenses)->getTotal();

        $this->props = ['expenseTabs' => [$this->expenseTabGroceries]];
    });

    test("should display the Expense Tab in the bills list", function () {
        Livewire::test(BillsList::class, $this->props)
            ->assertSee('Groceries');
    });

    test('should display the Expense Tab as a link', function () {
        Livewire::test(BillsList::class, $this->props)
            ->assertSeeHtmlInOrder([">", "Groceries", "</a>"]);
    });

    test('should link to the ExpenseTab index with the correct tab ID', function () {
        Livewire::test(BillsList::class, $this->props)
            ->assertSeeHtml('href="' . route('expense-tabs.index', ['tab' => $this->expenseTabGroceries->id]) . '"');
    });

    test('should display the total for the current monthly period', function () {
        Livewire::test(BillsList::class, $this->props)
            ->assertSee("100,00 €");
    });
});
