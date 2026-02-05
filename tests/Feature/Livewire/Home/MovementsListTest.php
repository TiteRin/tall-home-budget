<?php

namespace Tests\Feature\Livewire\Home;

use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Livewire\Home\Movements\MovementsList;
use App\Models\Bill;
use App\Services\Bill\BillsCollection;
use Livewire;

beforeEach(function () {
    $this->factory = test_factory()
        ->withHousehold(['name' => 'Test household', 'has_joint_account' => true])
        ->withMember(['first_name' => 'Alice'])
        ->withMember(['first_name' => 'Bob']);

    $this->memberAlice = $this->factory->members()->firstWhere('first_name', 'Alice');
    $this->memberBob = $this->factory->members()->firstWhere('first_name', 'Bob');

    $this->factory = $this->factory->withUser(['member_id' => $this->memberAlice->id]);

    $this->household = $this->factory->household();
    $this->user = $this->factory->user();

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

        test_factory()
            ->withMember(['first_name' => 'Alice'])
            ->withMember(['first_name' => 'Bob']);

        Livewire::test(MovementsList::class)
            ->assertSee("Aucun mouvement à afficher.");
    });
});

describe("when all is initialized", function () {

    beforeEach(function () {
        Bill::factory()->create([
            'name' => 'Loyer',
            'amount' => 70000,
            'distribution_method' => DistributionMethod::EQUAL,
            'member_id' => null,
            'household_id' => $this->factory->household()->id
        ]);

        $this->members = $this->factory->members()->all();
        $this->bills = new BillsCollection($this->factory->bills()->all());

        $this->incomes = [
            $this->memberAlice->id => new Amount(200000),
            $this->memberBob->id => new Amount(200000)
        ];
    });

    test("should display the members", function () {

        Livewire::test(MovementsList::class, ['incomes' => $this->incomes])
            ->assertSeeInOrder(['Alice Doe', 'Bob Doe']);
    });


    test('should display the recipients', function () {
        Livewire::test(MovementsList::class, ['incomes' => $this->incomes])
            ->assertSeeInOrder(['compte joint']);
    });

    test('should display the amounts', function () {
        Livewire::test(MovementsList::class, ['incomes' => $this->incomes])
            ->assertSeeInOrder(['350,00']);
    });

//    test('should display all the movements', function () {
//        Livewire::test(MovementsList::class, ['incomes' => $this->incomes])
//            ->assertSeeText('Alice Doe doit mettre 390,00 € sur le compte joint')
//            ->assertSeeText('Bob Doe doit mettre 310,00 € sur le compte joint');
//    });
});

