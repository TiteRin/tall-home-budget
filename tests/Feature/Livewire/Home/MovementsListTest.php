<?php

namespace Tests\Feature\Livewire\Home;

use App\Livewire\Home\MovementsList;
use Livewire;

test('should display the component', function () {
    Livewire::test(MovementsList::class)
        ->assertOk();
});

describe("When no members are passed", function () {
    test("should display a message", function () {
        Livewire::test(MovementsList::class)
            ->assertSee("Aucun mouvement à afficher.");
    });
});

describe("when members are passed", function () {
    beforeEach(function () {
        $this->household = bill_factory()->household();
        $this->memberJohn = bill_factory()->member(['first_name' => 'John'], $this->household);
        $this->memberMarie = bill_factory()->member(['first_name' => 'Marie'], $this->household);
        $this->props = [
            'members' => [$this->memberJohn, $this->memberMarie]
        ];
    });

    test("should display the members", function () {
        Livewire::test(MovementsList::class, $this->props)
            ->assertSeeInOrder([
                'John',
                'Marie'
            ]);
    });
});
