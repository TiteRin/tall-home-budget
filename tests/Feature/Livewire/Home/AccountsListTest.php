<?php

namespace Tests\Feature\Livewire\Home;

use App\Domains\ValueObjects\Amount;
use App\Livewire\Home\AccountsList;
use App\Rules\ValidAmount;
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

    describe('and has members', function () {

        beforeEach(function () {
            $this->memberDewey = bill_factory()->member([
                'first_name' => 'Dewey',
                'last_name' => 'Duck',
            ], $this->household);
            $this->memberHuey = bill_factory()->member([
                'first_name' => 'Huey',
                'last_name' => 'Duck',
            ], $this->household);
            $this->memberLouis = bill_factory()->member([
                'first_name' => 'Louis',
                'last_name' => 'Duck',
            ], $this->household);
        });

        test('should display the members', function () {
            Livewire::test(AccountsList::class)
                ->assertSeeInOrder(
                    [
                        'Dewey Duck',
                        'Huey Duck',
                        'Louis Duck',
                    ]
                );
        });

        test('should display a table', function () {
            Livewire::test(AccountsList::class)
                ->assertSeeHtml('<table');
        });

        test('should display an input for each member', function () {
            Livewire::test(AccountsList::class)
                ->assertSeeHtml('wire:model.blur="incomes.' . $this->memberDewey->id . '"')
                ->assertSeeHtml('wire:model.blur="incomes.' . $this->memberHuey->id . '"')
                ->assertSeeHtml('wire:model.blur="incomes.' . $this->memberLouis->id . '"');
        });

        test("shouldn’t be possible to set an invalid amount for income", function () {
            Livewire::test(AccountsList::class)
                ->set('incomes.' . $this->memberDewey->id, 'abc')
                ->assertHasErrors(['incomes.' . $this->memberDewey->id => ValidAmount::class]);
        });

        test('should convert the income to a correct amount', function () {
            Livewire::test(AccountsList::class)
                ->set('incomes.' . $this->memberDewey->id, '1540,25')
                ->assertSet('incomes.' . $this->memberDewey->id, "1 540,25 €");
        });

        test('should sum the incomes when an input is changed', function () {
            Livewire::test(AccountsList::class)
                ->set('incomes.' . $this->memberDewey->id, "2000")
                ->set('incomes.' . $this->memberHuey->id, "1000")
                ->set('incomes.' . $this->memberLouis->id, "1000")
                ->assertSet('totalIncomes', Amount::from("4000"));
        });

        test('should display the total', function () {
            Livewire::test(AccountsList::class)
                ->set('incomes.' . $this->memberDewey->id, "2000")
                ->set('incomes.' . $this->memberHuey->id, "2000")
                ->set('incomes.' . $this->memberLouis->id, "1000")
                ->assertSee('5 000,00 €');
        });

        test('should not display the total if not all inputs are filled', function () {
            Livewire::test(AccountsList::class)
                ->set('incomes.' . $this->memberDewey->id, "2000")
                ->set('incomes.' . $this->memberHuey->id, "2000")
                ->assertDontSee('4 000,00 €');
        });

        test('when an income is emptied, the total should be removed', function () {
            Livewire::test(AccountsList::class)
                ->set('incomes.' . $this->memberDewey->id, "2000")
                ->set('incomes.' . $this->memberHuey->id, "2000")
                ->set('incomes.' . $this->memberLouis->id, "1000")
                ->assertSee('5 000,00 €')
                ->set('incomes.' . $this->memberLouis->id, "")
                ->assertDontSee("2 000,00 €");
        });
    });
});
