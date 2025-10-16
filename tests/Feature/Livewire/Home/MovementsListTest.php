<?php

namespace Tests\Feature\Livewire\Home;

use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Livewire\Home\MovementsList;
use App\Services\Bill\BillsCollection;
use Livewire;

beforeEach(function () {
    $this->household = bill_factory()->household(['name' => 'Test household', 'has_joint_account' => true]);
});

test('should display the component', function () {
    Livewire::test(MovementsList::class)
        ->assertOk();
});

describe("When something is missing", function () {

    test("when no members should display a message", function () {
        Livewire::test(MovementsList::class)
            ->assertSee("Aucun mouvement à afficher.");
    });

    test("when no bills should display a message", function () {

        $memberAlice = bill_factory()->member(['first_name' => 'Alice'], $this->household);
        $memberBob = bill_factory()->member(['first_name' => 'Bob'], $this->household);

        Livewire::test(MovementsList::class)
            ->assertSee("Aucun mouvement à afficher.");
    });
});

describe("when all is initialized", function () {

    beforeEach(function () {
        $memberAlice = bill_factory()->member(['first_name' => 'Alice'], $this->household);
        $memberBob = bill_factory()->member(['first_name' => 'Bob'], $this->household);

        $loyer = bill_factory()->bill([
            'name' => 'Loyer',
            'amount' => 70000,
            'distribution_method' => DistributionMethod::EQUAL,
            'member_id' => null
        ], null, $this->household);

        $electricity = bill_factory()->bill([
            'name' => 'Électricité',
            'amount' => 9000,
            'distribution_method' => DistributionMethod::PRORATA,
        ], $memberAlice, $this->household);

        $internet = bill_factory()->bill([
            'name' => 'Internet',
            'amount' => 3000,
            'distribution_method' => DistributionMethod::PRORATA,
        ], $memberBob, $this->household);

        $veterinaire = bill_factory()->bill([
            'name' => 'Vétérinaire',
            'amount' => 10000,
            'distribution_method' => DistributionMethod::EQUAL,
        ], $memberBob, $this->household);

        $this->members = [$memberAlice, $memberBob];
        $this->bills = new BillsCollection([$loyer, $electricity, $internet, $veterinaire]);

        $this->incomes = [
            $memberAlice->id => new Amount(200000),
            $memberBob->id => new Amount(100000),
        ];

        $this->memberAlice = $memberAlice;
        $this->memberBob = $memberBob;
    });

    test("should display the members", function () {

        Livewire::test(MovementsList::class)
            ->set(['incomes' => $this->incomes])
            ->assertSeeInOrder(['Alice', 'Bob']);
    });
});

