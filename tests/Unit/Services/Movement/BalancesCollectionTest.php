<?php

use App\Domains\ValueObjects\Amount;
use App\Domains\ValueObjects\Balance;
use App\Models\Member;
use App\Services\Movement\BalancesCollection;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('BalancesCollection', function () {
    test('it filters creditors', function () {
        $member1 = Member::factory()->create();
        $member2 = Member::factory()->create();

        $balances = new BalancesCollection([
            new Balance($member1, new Amount(1000)),
            new Balance($member2, new Amount(-500)),
        ]);

        $creditors = $balances->getCreditors();

        expect($creditors)->toHaveCount(1)
            ->and($creditors->first()->isCreditor())->toBeTrue()
            ->and($creditors->first()->amount->toCents())->toBe(1000);
    });

    test('it filters debtors', function () {
        $member1 = Member::factory()->create();
        $member2 = Member::factory()->create();
        $member3 = Member::factory()->create();

        $balances = new BalancesCollection([
            new Balance($member1, new Amount(1000)),
            new Balance($member2, new Amount(-500)),
            new Balance($member3, new Amount(0)),
        ]);

        $debtors = $balances->getDebtors();

        // Amount(0) is considered debitor according to isDebitor() implementation: !isCreditor() and isCreditor() is > 0
        expect($debtors)->toHaveCount(2);
    });

    test('balance methods', function () {
        $member = Member::factory()->create();
        $balance = new Balance($member, new Amount(1000));

        expect($balance->abs()->toCents())->toBe(1000);

        $added = $balance->add(new Amount(500));
        expect($added->amount->toCents())->toBe(1500);

        $subtracted = $balance->subtract(new Amount(2000));
        expect($subtracted->amount->toCents())->toBe(-1000);
    });
});
