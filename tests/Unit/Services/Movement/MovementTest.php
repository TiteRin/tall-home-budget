<?php

namespace Tests\Unit\Services\Movement;

use App\Domains\ValueObjects\Amount;
use App\Services\Movement\Movement;
use Exception;

describe("Initialization", function () {

    beforeEach(function () {
        $this->household = bill_factory()->household([
            'name' => 'Test Household',
            'has_joint_account' => true
        ]);
        $this->memberAlice = bill_factory()->member(['first_name' => 'Alice'], $this->household);
        $this->memberBob = bill_factory()->member(['first_name' => 'Bob'], $this->household);
        $this->amount = new Amount(35000);
    });

    test("should initialize with correct values", function () {
        $movement = new Movement($this->memberAlice, $this->memberBob, $this->amount);
        expect($movement->memberFrom)->toBe($this->memberAlice)
            ->and($movement->memberTo)->toBe($this->memberBob)
            ->and($movement->amount)->toBe($this->amount);
    });

    test("if household as a joint account, should be able to initialize with one null member", function () {
        $movementA = new Movement($this->memberAlice, null, $this->amount);
        $movementB = new Movement(null, $this->memberBob, $this->amount);
    })->throwsNoExceptions();

    test("should throw an exception if memberFrom and memberTo are the same", function () {
        $movementA = new Movement($this->memberAlice, $this->memberAlice, $this->amount);
    })->throws(Exception::class, 'Can’t transfer money to yourself');

    test('if household as no joint account, should throw an exception if no member to transfer to', function () {
        $this->household->update(['has_joint_account' => false]);
        $movementA = new Movement($this->memberAlice, null, $this->amount);
    })->throws(Exception::class, 'No joint account to transfer to');

    test('if household as no joint account, should throw an exception if no member to transfer from', function () {
        $this->household->update(['has_joint_account' => false]);
        $movementA = new Movement(null, $this->memberAlice, $this->amount);
    })->throws(Exception::class, 'No joint account to transfer from');

    test('should throw an exception if both members are null', function () {
        $movement = new Movement(null, null, $this->amount);
    })->throws(Exception::class, "No valid member");

    test("should throw an exception if members are not from the same household", function () {
        $movement = new Movement(
            $this->memberAlice,
            bill_factory()->member(['first_name' => 'Charlie']),
            $this->amount
        );
    })->throws(Exception::class, "Members are not in the same household");
});


describe("Manipulation", function () {
    beforeEach(function () {
        $this->household = bill_factory()->household([
            'name' => 'Test Household',
            'has_joint_account' => true
        ]);
        $this->memberAlice = bill_factory()->member(['first_name' => 'Alice'], $this->household);
        $this->memberBob = bill_factory()->member(['first_name' => 'Bob'], $this->household);
        $this->memberCharlie = bill_factory()->member(['first_name' => 'Charlie'], $this->household);
        $this->memberDave = bill_factory()->member(['first_name' => 'Dave'], $this->household);

        $this->movementAB = new Movement($this->memberAlice, $this->memberBob, new Amount(35000));
        $this->movementBA = new Movement($this->memberBob, $this->memberAlice, new Amount(35000));
        $this->movementAC = new Movement($this->memberAlice, $this->memberCharlie, new Amount(10000));
        $this->movementBC = new Movement($this->memberBob, $this->memberCharlie, new Amount(15000));
        $this->movementCB = new Movement($this->memberCharlie, $this->memberBob, new Amount(10000));
        $this->movementCD = new Movement($this->memberCharlie, $this->memberDave, new Amount(20000));
    });

    describe("hasCommonMember", function () {
        test("should return true if has a common member", function () {
            expect($this->movementAB->hasCommonMember($this->movementBC))->toBeTrue();
        });

        test("should return false if has no common member", function () {
            expect($this->movementAB->hasCommonMember($this->movementCD))->toBeFalse();
        });
    });

    describe("Sum", function () {

        test('if movements have no members in common, should return an array with the same movements', function () {
            $movements = $this->movementAB->sum($this->movementCD);
            expect($movements)->toHaveCount(2)
                ->and($movements[0])->toBe($this->movementAB)
                ->and($movements[1])->toBe($this->movementCD);
        });

        test("if movements own to the same member, should return an array with the same movements", function () {
            $movements = $this->movementAB->sum($this->movementCB);
            expect($movements)->toHaveCount(2)
                ->and($movements[0])->toBe($this->movementAB)
                ->and($movements[1])->toBe($this->movementCB);
        });

        test("if movements belongs to the same member, should return an array with the same movements", function () {
            $movements = $this->movementAB->sum($this->movementAC);
            expect($movements)->toHaveCount(2)
                ->and($movements[0])->toBe($this->movementAB)
                ->and($movements[1])->toBe($this->movementAC);
        });

        test("if movements null each other, should return an empty array", function () {
            $movements = $this->movementAB->sum($this->movementBA);
            expect($movements)->toHaveCount(0);
        });

        test("if movements null each other but amount is not zero, should return an array with one movement", function () {
            $this->movementAB->amount = new Amount(10000);
            $this->movementBA->amount = new Amount(20000);

            $movements = $this->movementAB->sum($this->movementBA);
            expect($movements)->toHaveCount(1)
                ->and($movements[0]->memberFrom)->toBe($this->memberBob)
                ->and($movements[0]->memberTo)->toBe($this->memberAlice)
                ->and($movements[0]->amount)->toEqual(new Amount(10000));
        });

        test("should be able to sum movements", function () {
            $movements = $this->movementAB->sum($this->movementBC);
            expect($movements)->toHaveCount(2)
                ->and($movements[0]->memberFrom)->toBe($this->memberAlice)
                ->and($movements[0]->memberTo)->toBe($this->memberBob)
                ->and($movements[0]->amount)->toEqual(new Amount(10000))
                ->and($movements[1]->memberFrom)->toBe($this->memberAlice)
                ->and($movements[1]->memberTo)->toBe($this->memberCharlie)
                ->and($movements[1]->amount)->toEqual(new Amount(15000));
        });

        // A doit 100 à B
        // B doit 100 à A
        // retourne tableau vide

        // A doit 100 à B
        // A doit 100 à C
        // retourne tableau AB, AC

        // A doit 100 à B
        // B doit 100 à C
        // retourne tableau AC


    });
});
