<?php

namespace Tests\Feature\Livewire\Home;

use App\Livewire\Home\AccountsList;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire;

uses(RefreshDatabase::class);

describe("When no household exists", function () {

    test('should throw an exception', function () {
        Livewire::test(AccountsList::class);
    })->throws(Exception::class, 'No household exists');

});

describe('When a household exists', function () {
    beforeEach(function () {
        $this->household = bill_factory()->household();
    });

    test('should display the component', function () {
        Livewire::test(AccountsList::class)
            ->assertOk();
    });

    describe(" but has no members", function () {

        test('should display a message', function () {
            Livewire::test(AccountsList::class)
                ->assertSee('Aucun membre');
        });

        test('should display a link to the household manager', function () {
            Livewire::test(AccountsList::class)
                ->assertSee('Paramétrer le foyer')
                ->assertSeeHtml('href="' . route('household.settings') . '"');
        });
    });
});
