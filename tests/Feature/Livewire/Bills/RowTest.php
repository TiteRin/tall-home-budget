<?php

use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Livewire\Bills\Row;
use Livewire\Livewire;

it('renders successfully', function () {
    Livewire::test(Row::class, ['bill' => bill_factory()->bill()])
        ->assertStatus(200);
});

describe("Should display a bill", function () {
    beforeEach(function () {
        $this->bill = bill_factory()->bill([
            'name' => 'Test bill',
            'amount' => new Amount(17900),
            'distribution_method' => DistributionMethod::PRORATA
        ], bill_factory()->member(['first_name' => 'John', 'last_name' => 'Doe']));

        $this->livewire = Livewire::test(Row::class, ['bill' => $this->bill]);
    });

    test("should show the bill’s name", function () {
        $this->livewire->assertSee('Test bill');
    });

    test('should display the amount with french format', function () {
        $this->livewire->assertSee('179,00 ');
    });

    test('should display the distribution method', function () {
        $this->livewire->assertSee('Prorata');
    });

    test('should display the member name', function () {
        $this->livewire->assertSee('John Doe');
    });

    test('when no member, should display "Compte joint"', function () {
        $this->bill->member = null;
        Livewire::test(Row::class, ['bill' => $this->bill])
            ->assertSee('Compte joint');
    });

    test('should display Actions button', function () {
        $this->livewire
            ->assertSee('Modifier')
            ->assertSee('Supprimer');
    });
});
