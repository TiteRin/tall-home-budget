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

    test("should return a unique ID", function () {
        $movement = new Movement($this->memberAlice, $this->memberBob, $this->amount);
        expect($movement->getId())->toBe("{$this->memberAlice->id}-{$this->memberBob->id}-35000");
    });
});
