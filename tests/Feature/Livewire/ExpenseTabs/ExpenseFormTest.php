<?php

namespace Tests\Feature\Livewire\ExpenseTabs;

use App\Enums\DistributionMethod;
use App\Livewire\ExpenseTabs\ExpenseForm;
use App\Models\Expense;
use App\Models\ExpenseTab;
use Livewire\Livewire;

describe("ExpenseForm component", function () {

    beforeEach(function () {
        $this->factory = test_factory()
            ->withHousehold()
            ->withMember(['first_name' => 'Huwey'])
            ->withMember(['first_name' => 'Dewey'])
            ->withMember(['first_name' => 'Louie'])
            ->withUser();

        $this->expenseTab = ExpenseTab::factory()->create([
            'household_id' => $this->factory->household()->id,
        ]);

        $this->actingAs($this->factory->user());
    });

    test("it can create an expense", function () {
        $member = $this->factory->members()->first();

        Livewire::test(ExpenseForm::class, [
            'expenseTabId' => $this->expenseTab->id,
            'householdMembers' => $this->factory->members(),
            'defaultDistributionMethod' => DistributionMethod::EQUAL,
        ])
            ->set('newName', 'Candy for Ducklings')
            ->set('formattedNewAmount', '10.50')
            ->set('newDistributionMethod', DistributionMethod::EQUAL->value)
            ->set('newMemberId', $member->id)
            ->set('newSpentOn', '2026-02-01')
            ->call('addExpense');

        $this->assertDatabaseHas('expenses', [
            'name' => 'Candy for Ducklings',
            'amount' => 1050,
            'member_id' => $member->id,
            'expense_tab_id' => $this->expenseTab->id,
            'distribution_method' => DistributionMethod::EQUAL->value,
            'spent_on' => '2026-02-01 00:00:00',
        ]);
    });

    test("it can update an expense", function () {
        $member = $this->factory->members()->first();
        $expense = Expense::factory()->create([
            'expense_tab_id' => $this->expenseTab->id,
            'member_id' => $member->id,
            'name' => 'Old Name',
            'amount' => 1000,
            'spent_on' => '2026-01-01',
            'distribution_method' => DistributionMethod::EQUAL,
        ]);

        Livewire::test(ExpenseForm::class, [
            'expenseTabId' => $this->expenseTab->id,
            'householdMembers' => $this->factory->members(),
            'defaultDistributionMethod' => DistributionMethod::EQUAL,
            'expense' => $expense,
        ])
            ->assertSet('newName', 'Old Name')
            ->set('newName', 'New Name')
            ->set('formattedNewAmount', '20.00')
            ->call('saveExpense')
            ->assertSet('expense', null);

        $this->assertDatabaseHas('expenses', [
            'id' => $expense->id,
            'name' => 'New Name',
            'amount' => 2000,
        ]);
    });

    test("it can delete an expense", function () {
        $member = $this->factory->members()->first();
        $expense = Expense::factory()->create([
            'expense_tab_id' => $this->expenseTab->id,
            'member_id' => $member->id,
            'name' => 'To be deleted',
            'amount' => 1000,
        ]);

        Livewire::test(ExpenseForm::class, [
            'expenseTabId' => $this->expenseTab->id,
            'householdMembers' => $this->factory->members(),
            'expense' => $expense,
        ])
            ->call('deleteExpense')
            ->assertDispatched('refresh-expenses-table')
            ->assertSet('expense', null);

        $this->assertDatabaseMissing('expenses', ['id' => $expense->id]);
    });

    test("it can cancel edition", function () {
        $member = $this->factory->members()->first();
        $expense = Expense::factory()->create([
            'expense_tab_id' => $this->expenseTab->id,
            'member_id' => $member->id,
        ]);

        Livewire::test(ExpenseForm::class, [
            'expenseTabId' => $this->expenseTab->id,
            'householdMembers' => $this->factory->members(),
            'expense' => $expense,
        ])
            ->call('cancelEdition')
            ->assertDispatched('cancel-edit-expense');
    });

    test("it dispatches refresh-expenses-table event after adding", function () {
        $member = $this->factory->members()->first();

        Livewire::test(ExpenseForm::class, [
            'expenseTabId' => $this->expenseTab->id,
            'householdMembers' => $this->factory->members(),
            'defaultDistributionMethod' => DistributionMethod::EQUAL,
        ])
            ->set('newName', 'Popcorn')
            ->set('formattedNewAmount', '5.00')
            ->set('newDistributionMethod', DistributionMethod::EQUAL->value)
            ->set('newMemberId', $member->id)
            ->set('newSpentOn', '2026-02-01')
            ->call('addExpense')
            ->assertDispatched('refresh-expenses-table');
    });

    test("it can be initialized without defaultDistributionMethod", function () {
        Livewire::test(ExpenseForm::class, [
            'expenseTabId' => $this->expenseTab->id,
            'householdMembers' => $this->factory->members(),
        ])
            ->assertStatus(200)
            ->assertSet('newDistributionMethod', DistributionMethod::EQUAL->value);
    });

    test("it uses defaultDistributionMethod when provided", function () {
        Livewire::test(ExpenseForm::class, [
            'expenseTabId' => $this->expenseTab->id,
            'householdMembers' => $this->factory->members(),
            'defaultDistributionMethod' => DistributionMethod::PRORATA,
        ])
            ->assertStatus(200)
            ->assertSet('newDistributionMethod', DistributionMethod::PRORATA->value);
    });

    test("it resets to defaultDistributionMethod or EQUAL", function () {
        $component = Livewire::test(ExpenseForm::class, [
            'expenseTabId' => $this->expenseTab->id,
            'householdMembers' => $this->factory->members(),
        ]);

        $component->set('newDistributionMethod', DistributionMethod::PRORATA->value)
            ->call('resetFormFields')
            ->assertSet('newDistributionMethod', DistributionMethod::EQUAL->value);

        $componentWithDefault = Livewire::test(ExpenseForm::class, [
            'expenseTabId' => $this->expenseTab->id,
            'householdMembers' => $this->factory->members(),
            'defaultDistributionMethod' => DistributionMethod::PRORATA,
        ]);

        $componentWithDefault->set('newDistributionMethod', DistributionMethod::EQUAL->value)
            ->call('resetFormFields')
            ->assertSet('newDistributionMethod', DistributionMethod::PRORATA->value);
    });

    test("it can add an expense", function () {
        Livewire::test(ExpenseForm::class, [
            'expenseTabId' => $this->expenseTab->id,
            'householdMembers' => $this->factory->members(),
        ])
            ->set('newName', 'New Expense')
            ->set('newAmount', 1000)
            ->set('formattedNewAmount', '10,00 €')
            ->set('newMemberId', $this->factory->members()->first()->id)
            ->set('newSpentOn', now()->format('Y-m-d'))
            ->call('addExpense')
            ->assertHasNoErrors()
            ->assertDispatched('refresh-expenses-table')
            ->assertDispatched('notify', type: 'success');

        expect(Expense::where('name', 'New Expense')->count())->toBe(1);
    });

    test("it handles exception during addExpense", function () {
        $mock = \Mockery::mock(\App\Actions\Expenses\CreateExpense::class);
        $mock->shouldReceive('handle')->andThrow(new \Exception('Error'));
        app()->instance(\App\Actions\Expenses\CreateExpense::class, $mock);

        Livewire::test(ExpenseForm::class, [
            'expenseTabId' => $this->expenseTab->id,
            'householdMembers' => $this->factory->members(),
        ])
            ->set('newName', 'New Expense')
            ->set('newAmount', 1000)
            ->set('formattedNewAmount', '10,00 €')
            ->set('newMemberId', $this->factory->members()->first()->id)
            ->set('newSpentOn', now()->format('Y-m-d'))
            ->call('addExpense')
            ->assertDispatched('notify', type: 'error');
    });

    test("it provides distribution method options", function () {
        $component = Livewire::test(ExpenseForm::class, [
            'expenseTabId' => $this->expenseTab->id,
            'householdMembers' => $this->factory->members(),
        ]);

        $options = $component->get('distributionMethodOptions');
        expect($options)->toBeArray()->not->toBeEmpty();
    });

    test("it updates amount from formatted input", function () {
        Livewire::test(ExpenseForm::class, [
            'expenseTabId' => $this->expenseTab->id,
            'householdMembers' => $this->factory->members(),
        ])
            ->set('formattedNewAmount', '12,34')
            ->assertSet('newAmount', 1234)
            ->set('formattedNewAmount', '15.67')
            ->assertSet('newAmount', 1567)
            ->set('formattedNewAmount', '0')
            ->assertSet('newAmount', 0)
            ->set('formattedNewAmount', 'invalid')
            ->assertSet('newAmount', 0); // Assuming it keeps old value or 0
    });

    test("it shows empty state view if no members", function () {
        Livewire::test(ExpenseForm::class, [
            'expenseTabId' => $this->expenseTab->id,
            'householdMembers' => collect(),
        ])
            ->assertViewIs('livewire.expense-tabs.expenses-empty');
    });

    test("it provides household member options", function () {
        $component = Livewire::test(ExpenseForm::class, [
            'expenseTabId' => $this->expenseTab->id,
            'householdMembers' => $this->factory->members(),
        ]);

        $options = $component->get('householdMemberOptions');
        expect($options)->toBeArray()->not->toBeEmpty();
        expect(count($options))->toBeGreaterThanOrEqual(1);
    });
});
