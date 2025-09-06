<?php

namespace Tests\Feature\Livewire\Home;

use App\Exceptions\Households\MismatchedHouseholdException;
use App\Livewire\Home;
use Livewire;

beforeEach(function () {
    $this->household = bill_factory()->household([
        'has_joint_account' => true
    ]);
});

describe("Basic component tests", function () {

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

describe("Event listener tests", function () {

    beforeEach(function () {
        $this->memberJohn = bill_factory()->member(['first_name' => 'John'], $this->household);
        $this->memberMarie = bill_factory()->member(['first_name' => 'Marie'], $this->household);

        $this->billInternet = bill_factory()->bill(['name' => 'Internet', 'amount' => 2999], $this->memberJohn, $this->household);
        $this->billRent = bill_factory()->bill(['name' => 'Loyer', 'amount' => 67000], null, $this->household);
        $this->billPhone = bill_factory()->bill(['name' => 'Téléphone', 'amount' => 2498], $this->memberMarie, $this->household);
    });

    test('should listen to incomeModified event', function () {

        Livewire::test(Home::class, ['household' => $this->household])
            ->dispatch('incomeModified', memberId: $this->memberJohn->id, amount: 210000);
    })->throwsNoExceptions();

    test('when an income is modified, should add to the incomes state', function () {
        Livewire::test(Home::class, ['household' => $this->household])
            ->assertSet('incomes', function (array $incomes) {
                return empty($incomes);
            })
            ->dispatch('incomeModified', memberId: $this->memberJohn->id, amount: 210000)
            ->assertSet('incomes', function (array $incomes) {
                return $incomes[$this->memberJohn->id] == 210000;
            });
    });

    test('when an income is emptied, should remove from the incomes state', function () {
        Livewire::test(Home::class, ['household' => $this->household])
            ->set('incomes.' . $this->memberJohn->id, 210000)
            ->assertSet('incomes', function (array $incomes) {
                return $incomes[$this->memberJohn->id] == 210000;
            })
            ->dispatch('incomeModified', memberId: $this->memberJohn->id, amount: null)
            ->assertSet('incomes', function (array $incomes) {
                return empty($incomes);
            });
    });

    test('when an income is modified, should edit to the incomes state', function () {
        Livewire::test(Home::class, ['household' => $this->household])
            ->set('incomes.' . $this->memberJohn->id, 210000)
            ->assertSet('incomes', function (array $incomes) {
                return $incomes[$this->memberJohn->id] == 210000;
            })
            ->dispatch('incomeModified', memberId: $this->memberJohn->id, amount: 195000)
            ->assertSet('incomes', function (array $incomes) {
                return $incomes[$this->memberJohn->id] == 195000;
            });
    });

    test('when an income is modified, the member should be part of the household', function () {
        Livewire::test(Home::class, ['household' => $this->household])
            ->dispatch('incomeModified', memberId: 99, amount: 195000);
    })->throws(MismatchedHouseholdException::class);
});
