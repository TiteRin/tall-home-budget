<?php

namespace Tests\Feature\Livewire\Bills\Form;

use App\Actions\Bills\CreateBill;
use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Livewire\Bills\BillForm;
use App\Models\Bill;
use App\Services\Household\CurrentHouseholdServiceContract;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire;
use Mockery as m;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->household = bill_factory()->household(['name' => 'Test Household', 'has_joint_account' => true, 'default_distribution_method' => DistributionMethod::EQUAL]);
    $this->member = bill_factory()->member(['first_name' => 'John', 'last_name' => 'Doe'], $this->household);

    $this->householdService = m::mock(CurrentHouseholdServiceContract::class);

});

describe("when the creation succeeds", function () {

    beforeEach(function () {

        $this->householdService->shouldReceive('getCurrentHousehold')->once()->andReturn($this->household);

        $this->fakeAction = new class($this->householdService) extends CreateBill {

            private $hasBeenCalled = false;

            public function __construct(readonly private CurrentHouseholdServiceContract $householdService)
            {
                parent::__construct($this->householdService);
            }

            public function handle(string $billName, Amount $amount, DistributionMethod $distributionMethod, ?int $memberId = null): Bill
            {
                expect($billName)->toBe('Internet')
                    ->and($amount)->toEqual(new Amount(4200))
                    ->and($distributionMethod)->toBe(DistributionMethod::EQUAL)
                    ->and($memberId)->toBeNull();

                $this->hasBeenCalled = true;

                return parent::handle($billName, $amount, $distributionMethod, $memberId);
            }

            public function hasBeenCalled()
            {
                return $this->hasBeenCalled;

            }
        };

        app()->instance(CreateBill::class, $this->fakeAction);

        $this->livewireTest = Livewire::test(BillForm::class, [
            'householdMembers' => $this->household->members()->get(),
            'defaultDistributionMethod' => $this->household->getDefaultDistributionMethod(),
            'hasJointAccount' => $this->household->hasJointAccount()
        ])
            // Fill all fields
            ->set('newName', 'Internet')
            // Select "Compte joint"
            ->set('newMemberId', -1)
            ->set('formattedNewAmount', '42')
            // Keep the default value
            ->assertSet('newDistributionMethod', $this->household->getDefaultDistributionMethod()->value)
            ->call('addBill');
    });

    test("form should validate without errors", function () {
        $this->livewireTest->assertHasNoErrors();
    });

    test('the action has been called', function () {
        expect($this->fakeAction->hasBeenCalled())->toBeTrue();
    });

    test("a new bill should have been persisted", function () {

        $bill = Bill::where('name', 'Internet')->first();;

        expect($bill)
            ->toBeInstanceOf(Bill::class)
            ->and($bill->name)->toBe('Internet')
            ->and($bill->amount)->toEqual(new Amount(4200))
            ->and($bill->distribution_method)->toBe(DistributionMethod::EQUAL)
            ->and($bill->member)->toBeNull();
    });

    test("form should have been reset", function () {
        $this->livewireTest->assertSet('newName', '')
            ->assertSet('newMemberId', null)
            ->assertSet('newDistributionMethod', $this->household->getDefaultDistributionMethod()->value)
            ->assertSet('formattedNewAmount', '');
    });

    test('should dispatch a notification calling for refreshing the bills', function () {
        $this->livewireTest->assertDispatched('refreshBills');
    });
});


describe("when the creation fails", function () {

    beforeEach(function () {

        $this->household = bill_factory()->household(['name' => 'Test Household', 'has_joint_account' => true, 'default_distribution_method' => DistributionMethod::EQUAL]);
        $this->member = bill_factory()->member(['first_name' => 'John', 'last_name' => 'Doe'], $this->household);
        $this->householdService = m::mock(CurrentHouseholdServiceContract::class);

        $this->fakeAction = new class($this->householdService) extends CreateBill {

            private $hasBeenCalled = false;

            public function __construct(readonly private CurrentHouseholdServiceContract $householdService)
            {
                parent::__construct($this->householdService);
            }

            public function handle(string $billName, Amount $amount, DistributionMethod $distributionMethod, ?int $memberId = null): Bill
            {
                $this->hasBeenCalled = true;

                throw new Exception("Can’t save the bill");
            }

            public function hasBeenCalled()
            {
                return $this->hasBeenCalled;

            }
        };

        app()->instance(CreateBill::class, $this->fakeAction);

        $this->livewireTest = Livewire::test(BillForm::class, [
            'householdMembers' => $this->household->members()->get(),
            'defaultDistributionMethod' => $this->household->getDefaultDistributionMethod(),
            'hasJointAccount' => $this->household->hasJointAccount()
        ])
            // Fill all fields
            ->set('newName', 'Internet')
            // Select "Compte joint"
            ->set('newMemberId', -1)
            ->set('formattedNewAmount', '42')
            // Keep the default value
            ->assertSet('newDistributionMethod', $this->household->getDefaultDistributionMethod()->value)
            ->call('addBill');
    });

    test('the action has been called', function () {
        expect($this->fakeAction->hasBeenCalled())->toBeTrue();
    });

    test('the bill is non existant', function () {
        $bill = Bill::query()->latest('id')->first();
        expect($bill)->toBeNull();
    });

    test('should dispatch a notification about the exception', function () {
        $this->livewireTest->assertDispatched('notify', type: 'error', details: 'Can’t save the bill');
    });
});
