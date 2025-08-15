<?php

use App\Domains\ValueObjects\Amount;
use App\Livewire\Bills\Row;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(Row::class)
        ->assertStatus(200);
});

describe("Should display a bill", function () {
    beforeEach(function () {
        $this->bill = bill_factory()->bill([
            'name' => 'Test bill',
            'amount' => new Amount(17900),
        ]);

        $this->livewire = Livewire::test(Row::class, ['bill' => $this->bill]);
    });

    test("should show the bill’s name", function () {
        $this->livewire->assertSee('Test bill');
    });
});
