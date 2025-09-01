<?php

use App\Actions\Bills\CreateBill;
use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Livewire\BillForm;
use App\Models\Bill;
use App\Models\Household;
use App\Repositories\Contracts\BillRepository;
use App\Repositories\Fake\FakeBillRepository;
use App\Services\Household\HouseholdServiceContract;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Mockery as m;


afterEach(function () {
    m::close();
});

uses(RefreshDatabase::class);

test('when no members, should not display form', function () {

    Livewire::test(BillForm::class, [
        'householdMembers' => collect(),
    ])
        ->assertSee('Aucun membre')
        ->assertDontSeeHtml('wire:click="submit"');
});

describe("when the form is correctly parametered", function () {

    beforeEach(function () {

        $household = bill_factory()->household();
        $memberHuey = bill_factory()->member([
            'first_name' => 'Huey',
            'last_name' => 'Duck',
        ], $household);
        $memberDewey = bill_factory()->member([
            'first_name' => 'Dewey',
            'last_name' => 'Duck',
        ], $household);
        $memberLouis = bill_factory()->member([
            'first_name' => 'Louis',
            'last_name' => 'Duck',
        ], $household);;

        $householdMembers = [$memberHuey, $memberDewey, $memberLouis];

        $this->billFormProps = [
            'householdMembers' => collect($householdMembers),
            'defaultDistributionMethod' => DistributionMethod::EQUAL,
            'hasJointAccount' => false
        ];
    });

    test('should have form input to create a new bill', function () {
        Livewire::test(BillForm::class, $this->billFormProps)
            ->assertSeeHtmlInOrder([
                'wire:model="newName"',
                'wire:model.blur="formattedNewAmount"',
                'wire:model="newDistributionMethod"',
                'wire:model="newMemberId"'
            ]);
    });

    test('should offer distribution methods as options', function () {

        $distributionMethods = DistributionMethod::options();

        Livewire::test(BillForm::class, $this->billFormProps)
            ->assertSeeHtmlInOrder(
                array_values($distributionMethods)
            );
    });

    test('should offer the preferred distribution method by default', function () {
        Livewire::test(
            BillForm::class,
            array_merge(
                $this->billFormProps,
                ['defaultDistributionMethod' => DistributionMethod::PRORATA]
            )
        )
            ->assertSet('newDistributionMethod', DistributionMethod::PRORATA->value);
    });

    test('should offer members as options', function () {

        Livewire::test(BillForm::class, $this->billFormProps)
            ->assertSeeHtmlInOrder(
                ['Huey Duck', 'Dewey Duck', 'Louis Duck']
            );
    });

    test('when the prop "hasJointAccount" is true, should display "Compte joint" as an option', function () {
        Livewire::test(
            BillForm::class,
            array_merge(
                $this->billFormProps,
                ['hasJointAccount' => true]
            ))
            ->assertSeeHtml("Compte joint");
    });

    test('should offer "compte joint" as an option by default', function () {
        Livewire::test(
            BillForm::class,
            array_merge(
                $this->billFormProps,
                ['hasJointAccount' => true]
            ))
            ->assertSeeText('Compte joint');
    });

    test('should not offer "compte joint" as as option otherwise', function () {
        Livewire::test(
            BillForm::class,
            array_merge(
                $this->billFormProps,
                ['hasJointAccount' => false]
            ))->assertDontSeeText('Compte joint');
    });

    test('should display placeholder when no member is selected', function () {
        Livewire::test(BillForm::class, $this->billFormProps)
            ->assertSee('Membre du foyer');
    });

    test('should display placeholder if no amount given', function () {
        Livewire::test(BillForm::class, $this->billFormProps)
            ->assertSee('Montant');
    });

    test('should display formatted value if amount given', function () {
        Livewire::test(BillForm::class, $this->billFormProps)
            ->set('formattedNewAmount', '799,41')
            ->assertSet('newAmount', 79941)
            ->assertSet('formattedNewAmount', '799,41 €')
            ->assertSeeHtml('value="799,41 €');
    });

    describe("when the form is submitted with errors", function () {

        test('should validate required fields', function () {
            Livewire::test(BillForm::class, $this->billFormProps)
                ->call('addBill')
                ->assertHasErrors([
                    'newName' => 'required',
                    'formattedNewAmount' => 'required',
                    'newAmount' => 'required'
                ]);
        });

        test('newName should be a string with at least 1 character', function () {
            Livewire::test(BillForm::class, $this->billFormProps)
                ->set('newName', '')
                ->call('addBill')
                ->assertHasErrors([
                    'newName' => 'required'
                ])
                ->assertSee('Le champ "Nouvelle dépense" est requis.');
        });

        test('newAmount and formattedAmount should be numerical representation', function () {
            Livewire::test(BillForm::class, $this->billFormProps)
                ->set('formattedNewAmount', 'toto')
                ->call('addBill')
                ->assertHasErrors([
                    'newAmount' => 'gt:0',
                ])
                ->assertSee('Le champ "Montant" doit être supérieur à zéro.');
        });

        test('newDistributionMethod should be included in existing Distribution Method', function () {
            Livewire::test(BillForm::class, $this->billFormProps)
                ->set('newDistributionMethod', 'invalid-distribution-method')
                ->call('addBill')
                ->assertHasErrors([
                    'newDistributionMethod' => 'in'
                ])
                ->assertSet('newDistributionMethod', 'invalid-distribution-method')
                ->assertSee('Le champ "Méthode de distribution" n\'est pas valide.');
        });

        test('newAmount and formattedNewAmount should represent the same value', function () {
            Livewire::test(BillForm::class, $this->billFormProps)
                ->set('formattedNewAmount', '1000')
                ->set('newAmount', 200)
                ->call('addBill')
                ->assertHasErrors(['newAmount']);
        });

        test('newMemberId should be included in existing House members', function () {
            Livewire::test(BillForm::class, $this->billFormProps)
                ->set('newMemberId', 1000)
                ->call('addBill')
                ->assertHasErrors([
                    'newMemberId' => 'in'
                ])
                ->assertSet('newMemberId', 1000)
                ->assertSee('Le champ "Membre du foyer" n\'est pas valide.');
        });

        test('should not be possible to select a member who’s not in the household members', function () {

            $currentHousehold = bill_factory()->household();
            $anotherHousehold = bill_factory()->household();

            $memberAFromCurrentHousehold = bill_factory()->member([], $currentHousehold);
            $memberBFromCurrentHousehold = bill_factory()->member([], $currentHousehold);
            $memberAFromAnotherHousehold = bill_factory()->member([], $anotherHousehold);;

            Livewire::test(BillForm::class, [
                'householdMembers' => $currentHousehold->members()->get(),
                'defaultDistributionMethod' => $currentHousehold->getDefaultDistributionMethod(),
            ])
                ->set('newMemberId', $memberAFromAnotherHousehold->id)
                ->call('addBill')
                ->assertHasErrors([
                    'newMemberId' => 'in'
                ]);
        });

        test('when there is a joint account, should accept the value -1', function () {
            Livewire::test(BillForm::class, [
                'hasJointAccount' => true
            ])
                ->set('newMemberId', -1)
                ->call('addBill')
                ->assertHasNoErrors([
                    'newMemberId'
                ]);
        });

        test('newMemberId shouldn’t be null', function () {
            Livewire::test(BillForm::class)
                ->set('newMemberId', null)
                ->call('addBill')
                ->assertHasErrors([
                    'newMemberId' => 'required'
                ]);
        });
    });
});

describe("when the creation succeeds", function () {

    beforeEach(closure: function () {

        $this->household = bill_factory()->household(['name' => 'Test Household', 'has_joint_account' => true, 'default_distribution_method' => DistributionMethod::EQUAL]);
        $this->member = bill_factory()->member(['first_name' => 'John', 'last_name' => 'Doe'], $this->household);

        $this->fakeRepository = new FakeBillRepository();

        $householdId = 4444;
        $household = new Household();
        $household->setAttribute('id', $householdId);
        $this->householdService = m::mock(HouseholdServiceContract::class);
        $this->householdService->shouldReceive('getCurrentHousehold')->once()->andReturn($household);

        $this->fakeAction = new class($this->fakeRepository, $this->householdService) extends CreateBill {

            private $hasBeenCalled = false;

            public function __construct(readonly private BillRepository $billRepository, readonly private HouseholdServiceContract $householdService)
            {
                parent::__construct($this->billRepository, $this->householdService);
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

        $bill = $this->fakeRepository->getLastCreatedBill();

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

    beforeEach(closure: function () {

        $this->household = bill_factory()->household(['name' => 'Test Household', 'has_joint_account' => true, 'default_distribution_method' => DistributionMethod::EQUAL]);
        $this->member = bill_factory()->member(['first_name' => 'John', 'last_name' => 'Doe'], $this->household);

        $householdId = 4444;
        $household = new Household();
        $household->setAttribute('id', $householdId);
        $this->householdService = m::mock(HouseholdServiceContract::class);

        $this->fakeRepository = new FakeBillRepository();
        $this->fakeAction = new class($this->fakeRepository, $this->householdService) extends CreateBill {

            private $hasBeenCalled = false;

            public function __construct(readonly private BillRepository $billRepository, readonly private HouseholdServiceContract $householdService)
            {
                parent::__construct($this->billRepository, $this->householdService);
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
        $bill = $this->fakeRepository->getLastCreatedBill();
        expect($bill)->toBeNull();
    });

    test('should dispatch a notification about the exception', function () {
        $this->livewireTest->assertDispatched('notify', type: 'error', details: 'Can’t save the bill');
    });
});

describe("when a bill is edited", function () {
    beforeEach(function () {
        $household = bill_factory()->household();
        $memberHuey = bill_factory()->member([
            'first_name' => 'Huey',
            'last_name' => 'Duck',
        ], $household);
        $memberDewey = bill_factory()->member([
            'first_name' => 'Dewey',
            'last_name' => 'Duck',
        ], $household);
        $memberLouis = bill_factory()->member([
            'first_name' => 'Louis',
            'last_name' => 'Duck',
        ], $household);;

        $householdMembers = [$memberHuey, $memberDewey, $memberLouis];

        $bill = bill_factory()->bill([
            'name' => 'Internet',
            'amount' => 4200,
            'distribution_method' => DistributionMethod::PRORATA,
        ], $memberHuey, $household);

        $this->billFormProps = [
            'householdMembers' => collect($householdMembers),
            'defaultDistributionMethod' => DistributionMethod::EQUAL,
            'hasJointAccount' => false,
            'bill' => $bill,
        ];
    });

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

    describe('when the save button is clicked', function () {

        test('it should dispatch the billHasBeenUpdated event', function () {
            Livewire::test(BillForm::class, $this->billFormProps)
                ->call('saveBill')
                ->assertDispatched('billHasBeenUpdated');
        });

        test('it should dispatch even if fields are edited', function () {
            Livewire::test(BillForm::class, $this->billFormProps)
                ->set('newName', 'Fibre')
                ->set('formattedNewAmount', '52,50')
                ->set('newDistributionMethod', DistributionMethod::EQUAL->value)
                ->set('newMemberId', $this->billFormProps['householdMembers']->first()->id)
                ->call('saveBill')
                ->assertDispatched('billHasBeenUpdated');
        });

        test('it should keep the current form values (no reset on save)', function () {
            $firstMemberId = $this->billFormProps['householdMembers']->first()->id;

            Livewire::test(BillForm::class, $this->billFormProps)
                ->set('newName', 'Mobile')
                ->set('formattedNewAmount', '19,99')
                ->set('newDistributionMethod', DistributionMethod::EQUAL->value)
                ->set('newMemberId', $firstMemberId)
                ->call('saveBill')
                ->assertSet('newName', 'Mobile')
                ->assertSet('formattedNewAmount', '19,99 €')
                ->assertSet('newDistributionMethod', DistributionMethod::EQUAL->value)
                ->assertSet('newMemberId', $firstMemberId);
        });

        test('it should not dispatch unrelated events (like cancelEditBill)', function () {
            Livewire::test(BillForm::class, $this->billFormProps)
                ->call('saveBill')
                ->assertNotDispatched('cancelEditBill');
        });

    });
});
