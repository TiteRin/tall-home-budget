<?php

namespace Tests\Feature\Livewire\Bills\Form;

use App\Actions\Bills\UpdateBill;
use App\Enums\DistributionMethod;
use App\Livewire\BillForm;
use App\Models\Bill;
use App\Services\Household\HouseholdServiceContract;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire;
use Mockery as m;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->household = bill_factory()->household();

    $this->memberHuey = bill_factory()->member([
        'first_name' => 'Huey',
        'last_name' => 'Duck',
    ], $this->household);

    $this->memberDewey = bill_factory()->member([
        'first_name' => 'Dewey',
        'last_name' => 'Duck',
    ], $this->household);

    $this->memberLouis = bill_factory()->member([
        'first_name' => 'Louis',
        'last_name' => 'Duck',
    ], $this->household);;

    $householdMembers = [$this->memberHuey, $this->memberDewey, $this->memberLouis];

    $bill = bill_factory()->bill([
        'name' => 'Internet',
        'amount' => 4200,
        'distribution_method' => DistributionMethod::PRORATA,
    ], $this->memberHuey, $this->household);

    $this->billFormProps = [
        'householdMembers' => collect($householdMembers),
        'defaultDistributionMethod' => DistributionMethod::EQUAL,
        'hasJointAccount' => false,
        'bill' => $bill,
    ];
});

describe("when a bill is edited", function () {

    test("should accept a bill as a prop", function () {
        Livewire::test(BillForm::class, $this->billFormProps)
            ->assertSet('bill', $this->billFormProps['bill']);
    });

    test("the form should have the bill name in the newName field", function () {
        Livewire::test(BillForm::class, $this->billFormProps)
            ->assertSet('newName', 'Internet');
    });

    test("the form should have the bill amount in the newAmount field", function () {
        Livewire::test(BillForm::class, $this->billFormProps)
            ->assertSet('newAmount', 4200)
            ->assertSet('formattedNewAmount', '42,00 €');
    });

    test('the form should have the bill distribution method in the newDistributionMethod field', function () {
        Livewire::test(BillForm::class, $this->billFormProps)
            ->assertSet('newDistributionMethod', DistributionMethod::PRORATA->value);
    });

    test('the form should have the bill member in the newMemberId field', function () {
        Livewire::test(BillForm::class, $this->billFormProps)
            ->assertSet('newMemberId', $this->billFormProps['bill']->member->id);
    });

    test('if the bill references a member that’s not in the household, it should throw an error', function () {
        $this->billFormProps['bill']->member_id = 1000;
        Livewire::test(BillForm::class, $this->billFormProps);
    })->throws(Exception::class, "Incoherent data: the bill's member is not in the household members list.");

    test('the form should display save button', function () {
        Livewire::test(BillForm::class, $this->billFormProps)
            ->assertSee("Sauvegarder");
    });

    test('the form should display cancel button', function () {
        Livewire::test(BillForm::class, $this->billFormProps)
            ->assertSee("Annuler");
    });
});

describe('when the save button is clicked', function () {

    test('it should dispatch the billHasBeenUpdated event', function () {
        Livewire::test(BillForm::class, $this->billFormProps)
            ->call('saveBill')
            ->assertDispatched('billHasBeenUpdated');
    });

    test('it should not dispatch unrelated events (like cancelEditBill)', function () {
        Livewire::test(BillForm::class, $this->billFormProps)
            ->call('saveBill')
            ->assertNotDispatched('cancelEditBill');
    });
});

describe("when the update succeeds", function () {

    beforeEach(function () {

        $this->householdService = m::mock(HouseholdServiceContract::class);
        $this->householdService->shouldReceive('getCurrentHousehold')->once()->andReturn($this->household);

        $this->fakeAction = new class($this->householdService) extends UpdateBill {

            private $hasBeenCalled = false;

            public function __construct(readonly private HouseholdServiceContract $householdService)
            {
                parent::__construct($this->householdService);
            }

            public function handle(int $billId, array $data): Bill
            {
                $this->hasBeenCalled = true;

                return parent::handle($billId, $data);
            }

            public function hasBeenCalled()
            {
                return $this->hasBeenCalled;

            }
        };

        app()->instance(UpdateBill::class, $this->fakeAction);

        $this->component = Livewire::test(BillForm::class, $this->billFormProps)
            ->set('newName', 'Nouveau nom')
            ->set('formattedNewAmount', '7000')
            ->set('newDistributionMethod', DistributionMethod::EQUAL->value)
            ->set('newMemberId', $this->memberLouis->id)
            ->call('saveBill');
    });

    test('should validate the form', function () {
        $this->component->assertHasNoErrors();
    });

    test('the action should have been called', function () {
        expect($this->fakeAction->hasBeenCalled())->toBeTrue();
    });
});

describe("when the update fails", function () {

});
