<?php

namespace Test\Unit;

use App\Domains\ValueObjects\Amount;
use App\Domains\ValueObjects\Balance;
use App\Models\Member;

it("should create a balance with a member and an amount", function () {
    $member = Member::factory()->make(['first_name' => 'Alice']);
    $amount = new Amount(3900);

    $balance = new Balance($member, $amount);
    expect($balance->member)->toBe($member)
        ->and($balance->amount)->toBe($amount);
});

it("should detect if a member is a creditor or a debitor", function () {
    $member = Member::factory()->make(['first_name' => 'Alice']);

    $creditor = new Balance($member, new Amount(10000));
    $debitor = new Balance($member, new Amount(-5000));

    expect($creditor->isCreditor())->toBeTrue()
        ->and($creditor->isDebitor())->toBeFalse()
        ->and($debitor->isCreditor())->toBeFalse()
        ->and($debitor->isDebitor())->toBeTrue();

});

it("should return the absolute amount", function () {
    $balance = new Balance(
        Member::factory()->make(['first_name' => 'Alice']),
        new Amount(-10000)
    );

    expect($balance->abs())->toEqual(new Amount(10000));
});

it("should add substrac amounts immutably", function () {
    $member = Member::factory()->make(['first_name' => 'Alice']);
    $balance = new Balance($member, new Amount(10000));

    $newBalance = $balance->add(new Amount(5000));

    expect($newBalance)->not->toBe($balance)
        ->and($newBalance->amount)->toEqual(new Amount(15000))
        ->and($balance->amount)->toEqual(new Amount(10000));

    $reduced = $newBalance->subtract(new Amount(7000));

    expect($reduced->amount)->toEqual(new Amount(8000));
});

