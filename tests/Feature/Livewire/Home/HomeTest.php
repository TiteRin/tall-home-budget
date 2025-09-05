<?php

namespace Tests\Feature\Livewire\Home;

use App\Livewire\Home;
use Livewire;

beforeEach(function () {
    $this->household = bill_factory()->household();
});

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
