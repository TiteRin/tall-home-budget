<?php

use App\Enums\DistributionMethod;
use App\Livewire\BillForm;
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
