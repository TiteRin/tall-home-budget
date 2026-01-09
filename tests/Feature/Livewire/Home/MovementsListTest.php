<?php

namespace Tests\Feature\Livewire\Home;

use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Livewire\Home\Movements\MovementsList;
use App\Models\User;
use App\Services\Bill\BillsCollection;
use Livewire;

beforeEach(function () {
    $this->household = bill_factory()->household(['name' => 'Test household', 'has_joint_account' => true]);
    $this->memberAlice = bill_factory()->member(['first_name' => 'Alice'], $this->household);
    $this->memberBob = bill_factory()->member(['first_name' => 'Bob'], $this->household);
    $this->user = User::factory()->create(['member_id' => $this->memberAlice->id]);
    $this->actingAs($this->user);
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
        ], $this->memberAlice, $this->household);

        $internet = bill_factory()->bill([
            'name' => 'Internet',
            'amount' => 3000,
            'distribution_method' => DistributionMethod::PRORATA,
        ], $this->memberBob, $this->household);

        $veterinaire = bill_factory()->bill([
            'name' => 'Vétérinaire',
            'amount' => 10000,
            'distribution_method' => DistributionMethod::EQUAL,
        ], $this->memberBob, $this->household);

        $this->members = [$this->memberAlice, $this->memberBob];
        $this->bills = new BillsCollection([$loyer, $electricity, $internet, $veterinaire]);

        $this->incomes = [
            $this->memberAlice->id => new Amount(200000),
            $this->memberBob->id => new Amount(100000),
        ];
    });

    test("should display the members", function () {

        Livewire::test(MovementsList::class, ['incomes' => $this->incomes])
            ->assertSeeInOrder(['Alice Doe', 'Bob Doe']);
    });


    test('should display the recipients', function () {
        Livewire::test(MovementsList::class, ['incomes' => $this->incomes])
            ->assertSeeInOrder(['Compte joint', 'Compte joint']);
    });

    test('should display the amounts', function () {
        Livewire::test(MovementsList::class, ['incomes' => $this->incomes])
            ->assertSeeInOrder(['390,00', '310,00']);
    });

    test('should display all the movements', function () {
        Livewire::test(MovementsList::class, ['incomes' => $this->incomes])
            ->assertSee('Alice Doe doit 390,00 € à Compte joint')
            ->assertSee('Bob Doe doit 310,00 € à Compte joint');
    });
});

