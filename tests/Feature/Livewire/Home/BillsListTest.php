<?php

namespace Tests\Feature\Livewire\Home;

use App\Livewire\Home\BillsList;
use Livewire;

test('should display the component', function () {
    Livewire::test(BillsList::class)
        ->assertOk();
});

describe("no bills are passed", function () {
    test('should display a message', function () {
        Livewire::test(BillsList::class)
            ->assertSee("Aucune dépense");
    });

    test('should display a link to the bills manager', function () {
        Livewire::test(BillsList::class)
            ->assertSee("Paramétrer les dépenses")
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

