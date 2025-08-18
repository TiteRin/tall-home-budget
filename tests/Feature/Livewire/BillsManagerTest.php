<?php

use App\Livewire\BillsManager;
use App\Repositories\BillRepository;
use App\Repositories\FakeBillRepository;
use Livewire\Livewire;

beforeEach(function () {
    $this->fakeRepository = new FakeBillRepository();
    $this->app->instance(BillRepository::class, $this->fakeRepository);

    $this->household = bill_factory()->household();
});

test('it displays "Les dépenses" as a title', function () {

    Livewire::test(BillsManager::class)
        ->assertSeeText('Dépenses du foyer');
});


test('should display an empty table if no bills', function () {

    Livewire::test(BillsManager::class)
        ->assertSeeText('Aucune dépense');
});

test('should use the BillForm component to add a bill', function () {
    Livewire::test(BillsManager::class)
        ->assertSeeLivewire('bill-form');
});

describe('when there’s a list of 5 bills', function () {
    beforeEach(function () {
        $member = bill_factory()->member([], $this->household);
        $this->bills = bill_factory()->bills(5, [], $member, $this->household);
    });

    test('the component bill-row should have been called', function () {
        Livewire::test(BillsManager::class)
            ->assertSeeLivewire('bills.row');
    });
});
