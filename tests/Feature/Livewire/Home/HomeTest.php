<?php

namespace Tests\Feature\Livewire\Home;

use App\Livewire\Home;
use Livewire;

beforeEach(function () {
    $this->household = bill_factory()->household([
        'has_joint_account' => true
    ]);
});

describe("When the component is mounted", function () {

    test("when the household has no member, its state should have no members", function () {
        Livewire::test(Home::class, ['household' => $this->household])
            ->assertSet('members', function ($members) {
                return $members->isEmpty();
            });
    });

    test('when the household has members, its state should have the members', function () {
        $memberJohn = bill_factory()->member(['first_name' => 'John'], $this->household);
        $memberMarie = bill_factory()->member(['first_name' => 'Marie'], $this->household);

        Livewire::test(Home::class, ['household' => $this->household])
            ->assertSet('members', function ($members) {
                return $members->pluck('first_name')->toArray() == ['John', 'Marie'];
            });
    });

    test('when the household has no bills, its state should have no bills', function () {
        Livewire::test(Home::class, ['household' => $this->household])
            ->assertSet('bills', function ($bills) {
                return $bills->isEmpty();
            });
    });

    test('when the household has bills, its state should have the bills', function () {
        $billInternet = bill_factory()->bill(['name' => 'Internet'], null, $this->household);
        $billRent = bill_factory()->bill(['name' => 'Loyer'], null, $this->household);
        $billPhone = bill_factory()->bill(['name' => 'Téléphone'], null, $this->household);

        Livewire::test(Home::class, ['household' => $this->household])
            ->assertSet('bills', function ($bills) {
                return $bills->pluck('name')->toArray() == ['Internet', 'Loyer', 'Téléphone'];
            });
    });
});


describe("Basic component test", function () {

    test('should display the component', function () {
        Livewire::test(Home::class, ['household' => $this->household])
            ->assertStatus(200);
    });

    test('should have a AccountsList component', function () {
        Livewire::test(Home::class, ['household' => $this->household])
            ->assertSeeLivewire('home.accounts-list');
    });

    test('should have a GeneralInformation component', function () {
        Livewire::test(Home::class, ['household' => $this->household])
            ->assertSeeLivewire('home.general-information');
    });

    test('should have a BillsList component', function () {
        Livewire::test(Home::class, ['household' => $this->household])
            ->assertSeeLivewire('home.bills-list');
    });

    test('should have a MovementsList component', function () {
        Livewire::test(Home::class, ['household' => $this->household])
            ->assertSeeLivewire('home.movements-list');
    });
});
