<?php

namespace Tests\Feature\Livewire\ExpenseTabs;

use App\Livewire\ExpenseTabs\ExpenseTabForm;
use Livewire\Livewire;

describe('Expense Tab Form', function () {

    beforeEach(function () {
        $this->factory = test_factory()
            ->withHousehold()
            ->withMember()
            ->withUser();
        $this->actingAs($this->factory->user());
    });

    test('should display inputs for name and period start day', function () {
        Livewire::test(ExpenseTabForm::class)
            ->assertSee('Nom')
            ->assertSee('Jour de démarrage de la période');
    });

    describe("Validation", function () {
        test('should validate name is required', function () {
            Livewire::test(ExpenseTabForm::class)
                ->set('newName', '')
                ->set('newStartDay', 5)
                ->call('saveExpenseTab')
                ->assertHasErrors(['newName' => 'required']);
        });

        test('should validate newStartDay can’t be greater than 31', function () {
            Livewire::test(ExpenseTabForm::class)
                ->set('newName', 'Groceries')
                ->set('newStartDay', 45)
                ->call('saveExpenseTab')
                ->assertHasErrors(['newStartDay' => 'max']);
        });

        test('should validate newStartDay can’t be lower than 1', function () {
            Livewire::test(ExpenseTabForm::class)
                ->set('newName', 'Groceries')
                ->set('newStartDay', 0)
                ->call('saveExpenseTab')
                ->assertHasErrors(['newStartDay' => 'min']);
        });

        test('should validate newStartDay can’t be a decimal', function () {
            Livewire::test(ExpenseTabForm::class)
                ->set('newName', 'Groceries')
                ->set('newStartDay', 5.5)
                ->assertSet('newStartDay', 5);
        });

        test('should validate newStartDay can’t be empty', function () {
            Livewire::test(ExpenseTabForm::class)
                ->set('newName', 'Groceries')
                ->set('newStartDay', '')
                ->call('saveExpenseTab')
                ->assertSet('newStartDay', 1);
        });
    });

    describe("When creating a new tab", function () {

        beforeEach(function () {
            $this->livewire = Livewire::test(ExpenseTabForm::class)
                ->set('newName', 'Groceries')
                ->set('newStartDay', 5)
                ->call('saveExpenseTab');
        });

        test('should create a new expense tab', function () {
            $this->livewire->assertHasNoErrors();
            $this->assertDatabaseHas('expense_tabs',
                [
                    'household_id' => $this->factory->household()->id,
                    'name' => 'Groceries',
                    'from_day' => 5
                ]);
        });

        test('should reset the form', function () {
            $this->livewire->assertHasNoErrors()
                ->assertSet('newName', '')
                ->assertSet('newStartDay', 1);
        });


        test('should dispatch an event', function () {
            $this->livewire->assertHasNoErrors()
                ->assertDispatched('expenseTabCreated');
        });
    });

    describe("When editing a tab", function () {

    });
});


