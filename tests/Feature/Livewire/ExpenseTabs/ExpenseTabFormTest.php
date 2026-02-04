<?php

namespace Tests\Feature\Livewire\ExpenseTabs;

use App\Actions\ExpenseTab\UpdateExpenseTab;
use App\Livewire\ExpenseTabs\ExpenseTabForm;
use App\Models\ExpenseTab;
use Livewire\Livewire;
use Mockery;

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
                ->call('submitForm')
                ->assertHasErrors(['newName' => 'required']);
        });

        test('should validate newStartDay can’t be greater than 31', function () {
            Livewire::test(ExpenseTabForm::class)
                ->set('newName', 'Groceries')
                ->set('newStartDay', 45)
                ->call('submitForm')
                ->assertHasErrors(['newStartDay' => 'max']);
        });

        test('should validate newStartDay can’t be lower than 1', function () {
            Livewire::test(ExpenseTabForm::class)
                ->set('newName', 'Groceries')
                ->set('newStartDay', 0)
                ->call('submitForm')
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
                ->call('submitForm')
                ->assertSet('newStartDay', 1);
        });
    });

    describe("When creating a new tab", function () {

        beforeEach(function () {
            $this->livewire = Livewire::test(ExpenseTabForm::class)
                ->set('newName', 'Groceries')
                ->set('newStartDay', 5)
                ->call('submitForm');
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
                ->assertDispatched('refresh-expense-tabs');
        });
    });

    describe("When editing a tab", function () {

        beforeEach(function () {

            $this->currentExpenseTab = ExpenseTab::factory()->create([
                'household_id' => $this->factory->household()->id,
                'name' => 'Grocerie',
                'from_day' => 5
            ]);
        });

        test("should display the editing tab info", function () {
            Livewire::test(ExpenseTabForm::class, ['currentExpenseTabId' => $this->currentExpenseTab->id])
                ->assertSet('newName', 'Grocerie')
                ->assertSet('newStartDay', 5);
        });

        test("should save the new information", function () {
            Livewire::test(ExpenseTabForm::class, ['currentExpenseTabId' => $this->currentExpenseTab->id])
                ->set('newName', 'Groceries')
                ->call('submitForm');

            $this->assertDatabaseHas('expense_tabs',
                [
                    'id' => $this->currentExpenseTab->id,
                    'name' => 'Groceries',
                ]);
        });

        test('should dispatch an event', function () {

            Livewire::test(ExpenseTabForm::class, ['currentExpenseTabId' => $this->currentExpenseTab->id])
                ->set('newName', 'Groceries')
                ->call('submitForm')
                ->assertDispatched('refresh-expense-tabs');
        });

        test('should update expense tab', function () {
            $mock = Mockery::mock(UpdateExpenseTab::class);
            $mock->shouldReceive('handle')->once()->andReturn(ExpenseTab::factory()->make());
            app()->instance(UpdateExpenseTab::class, $mock);

            Livewire::test(ExpenseTabForm::class, ['currentExpenseTabId' => $this->currentExpenseTab->id])
                ->set('newName', 'New Name')
                ->call('saveExpenseTab')
                ->assertHasNoErrors()
                ->assertDispatched('refresh-expense-tabs');
        });

        test('should throw exception if household is null', function () {
            // Mock householdService to return null
            $mockService = Mockery::mock(\App\Services\Household\CurrentHouseholdServiceContract::class);
            $mockService->shouldReceive('getCurrentHousehold')->andReturn(null);
            app()->instance(\App\Services\Household\CurrentHouseholdServiceContract::class, $mockService);

            $this->expectException(\Exception::class);
            Livewire::test(ExpenseTabForm::class)
                ->call('submitForm');
        });
    });
});


