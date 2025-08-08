<?php

use App\Actions\CreateBill;
use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Livewire\BillForm;
use App\Models\Bill;
use App\Models\Household;
use App\Models\Member;
use App\Services\HouseholdService;
use Livewire\Livewire;


test('should have form input to create a new bill', function () {

    Livewire::test(BillForm::class)
        ->assertSeeHtmlInOrder([
            'wire:model="newName"',
            'wire:model.blur="formattedNewAmount"',
            'wire:model="newDistributionMethod"',
            'wire:model="newMemberId"'
        ]);
});

test('should offer distribution methods as options', function () {

    $distributionMethods = DistributionMethod::options();

    Livewire::test(BillForm::class)
        ->assertSeeHtmlInOrder(
            array_values($distributionMethods)
        );
});

test('should offer household members as options', function () {
    $memberHuey = Member::factory()->create([
        'first_name' => 'Huey',
        'last_name' => 'Duck',
    ]);
    $memberDewey = Member::factory()->create([
        'first_name' => 'Dewey',
        'last_name' => 'Duck',
        'household_id' => $memberHuey->household_id,
    ]);
    $memberLouis = Member::factory()->create([
        'first_name' => 'Louis',
        'last_name' => 'Duck',
        'household_id' => $memberHuey->household_id,
    ]);

    $householdMembers = [$memberHuey, $memberDewey, $memberLouis];

    Livewire::test(BillForm::class,
        [
            'householdMembers' => collect($householdMembers)
        ])
        ->assertSeeHtmlInOrder(
            array_map(function (Member $member) {
                return $member->full_name;
            }, $householdMembers)
        );
});

test('when household has a joint account, it should display "Compte joint" as an option', function () {
    Livewire::test(BillForm::class, [
        'householdMembers' => collect(),
        'hasJointAccount' => true
    ])
        ->assertSeeHtml("Compte joint");
});

test('should display placeholder if no amount given', function () {
    Livewire::test(BillForm::class)
        ->assertSee('Montant');
});

test('should display formatted value if amount given', function () {
    Livewire::test(BillForm::class)
        ->set('formattedNewAmount', '799,41')
        ->assertSet('newAmount', 79941)
        ->assertSet('formattedNewAmount', '799,41 €')
        ->assertSeeHtml('value="799,41 €');
});

test('should offer "compte joint" as an option by default', function () {

    Livewire::test(BillForm::class)->assertSeeText('Compte joint');
});

test('should not offer "compte joint" as as option otherwise', function () {

    Livewire::test(BillForm::class, [
        'hasJointAccount' => false
    ])->assertDontSeeText('Compte joint');
});

test('should offer the preferred distribution method by default', function () {
    Livewire::test(BillForm::class, ['defaultDistributionMethod' => DistributionMethod::PRORATA])
        ->assertSet('newDistributionMethod', DistributionMethod::PRORATA->value);
});

test('should validate required fields', function () {
    Livewire::test(BillForm::class)
        ->call('submit')
        ->assertHasErrors([
            'newName' => 'required',
            'formattedNewAmount' => 'required',
            'newAmount' => 'required'
        ]);
});

test('newName should be a string with at least 1 character', function () {
    Livewire::test(BillForm::class)
        ->set('newName', '')
        ->call('submit')
        ->assertHasErrors([
            'newName' => 'required'
        ])
        ->assertSee('Le champ "Nouvelle dépense" est requis.');
});

test('newAmount and formattedAmount should be numerical representation', function () {
    Livewire::test(BillForm::class)
        ->set('formattedNewAmount', 'toto')
        ->call('submit')
        ->assertHasErrors([
            'newAmount' => 'gt:0',
        ])
        ->assertSee('Le champ "Montant" doit être supérieur à zéro.');
});

test('newDistributionMethod should be included in existing Distribution Method', function () {
    Livewire::test(BillForm::class)
        ->set('newDistributionMethod', 'invalid-distribution-method')
        ->call('submit')
        ->assertHasErrors([
            'newDistributionMethod' => 'in'
        ])
        ->assertSet('newDistributionMethod', 'invalid-distribution-method')
        ->assertSee('Le champ "Méthode de distribution" n\'est pas valide.');
});

test('newAmount and formattedNewAmount should represent the same value', function () {
    Livewire::test(BillForm::class)
        ->set('formattedNewAmount', '1000')
        ->set('newAmount', 200)
        ->call('submit')
        ->assertHasErrors(['newAmount']);
});

test('newMemberId should be included in existing House members', function () {
    Livewire::test(BillForm::class)
        ->set('newMemberId', 1000)
        ->call('submit')
        ->assertHasErrors([
            'newMemberId' => 'in'
        ])
        ->assertSet('newMemberId', 1000)
        ->assertSee('Le champ "Membre du foyer" n\'est pas valide.');
});

test('newMemberId should be included in the current household members', function () {

    $householdService = new HouseholdService();
    Household::create([
        'name' => 'Test Current Household',
        'has_joint_account' => false,
        'default_distribution_method' => DistributionMethod::EQUAL,
    ]);
    $currentHousehold = $householdService->getCurrentHousehold();
    $anotherHousehold = Household::create([
        'name' => 'Test Another Household',
        'has_joint_account' => true,
        'default_distribution_method' => DistributionMethod::EQUAL,
    ]);

    assert($currentHousehold !== null);

    $memberAFromCurrentHousehold = Member::create([
        'household_id' => $currentHousehold->id,
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);
    $memberBFromCurrentHousehold = Member::create([
        'household_id' => $currentHousehold->id,
        'first_name' => 'Jane',
        'last_name' => 'Doe',
    ]);

    $memberAFromAnotherHousehold = Member::create([
        'household_id' => $anotherHousehold->id,
        'first_name' => 'Dewey',
        'last_name' => 'Duck',
    ]);

    Livewire::test(BillForm::class, [
        'householdMembers' => $currentHousehold->members()->get(),
        'defaultDistributionMethod' => $currentHousehold->getDefaultDistributionMethod(),
    ])
        ->set('newMemberId', $memberAFromAnotherHousehold->id)
        ->call('submit')
        ->assertHasErrors([
            'newMemberId' => 'in'
        ]);
});

test('when there is a joint account, should accept a null value', function () {
    Livewire::test(BillForm::class, [
        'hasJointAccount' => true
    ])
        ->set('newMemberId', null)
        ->call('submit')
        ->assertHasNoErrors([
            'newMemberId'
        ]);
});

test('where there is no joint account, newMemberId shouldn’t be null', function () {
    Livewire::test(BillForm::class, [
        'hasJointAccount' => false
    ])
        ->set('newMemberId', null)
        ->call('submit')
        ->assertHasErrors([
            'newMemberId' => 'required'
        ]);
});

describe("create a new bill from the form", function () {

    beforeEach(function () {
        $this->household = Household::create(['name' => 'Test Household']);
        $this->member = Member::create(['household_id' => $this->household->id, 'first_name' => 'John', 'last_name' => 'Doe']);

        $this->called = false;
        $this->fakeAction = new class($this->household, $this->member) extends CreateBill {
            public function __construct(private Household $household, private Member $member)
            {
            }

            public function handle(string $billName, Amount $amount, DistributionMethod $distributionMethod, int $householdId, ?int $memberId = null): Bill
            {
                expect($billName)->toBe('Internet')
                    ->and($amount)->toEqual(new Amount(4200))
                    ->and($distributionMethod)->toBe(DistributionMethod::EQUAL)
                    ->and($householdId)->toEqual($this->household->id)
                    ->and($memberId)->toBe($this->member->id);

                return new Bill([
                    'name' => $billName
                ]);
            }
        };

        app()->instance(CreateBill::class, $this->fakeAction);

        $this->livewireTest = Livewire::test(BillForm::class, [
            'householdMembers' => $this->household->members()->get(),
            'defaultDistributionMethod' => $this->household->getDefaultDistributionMethod(),
        ])
            // Fill all fields
            ->set('newName', 'Internet')
            ->set('newMemberId', $this->member->id)
            ->set('formattedNewAmount', '42')
            ->assertSet('newDistributionMethod', $this->household->getDefaultDistributionMethod()->value)
            ->call('submit');
    });

    test("form should validate without errors", function () {
        $this->livewireTest->assertHasNoErrors();
    });

    test("a new bill should have been persisted", function () {

    });

    test("form should have been reset", function () {
        $this->livewireTest->assertSet('newName', '')
            ->assertSet('newMemberId', null)
            ->assertSet('newDistributionMethod', $this->household->getDefaultDistributionMethod()->value)
            ->assertSet('formattedNewAmount', '');
    });

    test("a success message should be visible", function () {

    });
});

//
//test('should create a new bill', function () {
//    // Create a fake repository to avoid database dependency
//    $fakeBillRepository = new FakeBillRepository();
//
//    // Bind the fake repository to the container for this test
//    app()->instance(BillRepository::class, $fakeBillRepository);
//
//    $currentHousehold = Household::create([
//        'name' => 'Test Current Household',
//        'has_joint_account' => false,
//        'default_distribution_method' => DistributionMethod::EQUAL,
//    ]);
//
//    $memberJohnDoe = Member::create([
//        'household_id' => $currentHousehold->id,
//        'first_name' => 'John',
//        'last_name' => 'Doe',
//    ]);
//
//    Livewire::test(BillForm::class, [
//        'householdMembers' => $currentHousehold->members()->get(),
//        'defaultDistributionMethod' => $currentHousehold->getDefaultDistributionMethod(),
//    ])
//        // Fill all fields
//        ->set('newName', 'Électricité')
//        ->set('newMemberId', $memberJohnDoe->id)
//        ->set('formattedNewAmount', '179')
//        ->assertSet('newDistributionMethod', $currentHousehold->getDefaultDistributionMethod()->value)
//        ->call('submit')
//        ->assertHasNoErrors();
//
//    // Verify a new bill has been saved in the fake repository
//    $bill = $fakeBillRepository->getLastCreatedBill();
//    expect($bill)
//        ->toBeInstanceOf(Bill::class)
//        ->and($bill->household_id)->toBe($currentHousehold->id)
//        ->and($bill->member_id)->toBe($memberJohnDoe->id)
//        ->and($bill->amount->value())->toBe(17900)
//        ->and($bill->distribution_method)->toBe(DistributionMethod::EQUAL);
//});

// TODO : Form should be empty after saving
