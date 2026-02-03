<?php

use App\Domains\ValueObjects\Amount;
use App\Domains\ValueObjects\Charge;
use App\Enums\DistributionMethod;
use App\Models\Member;

it('creates a charge with zero amount by default', function () {
    $charge = Charge::create();

    expect($charge->getAmountOrZero())->toEqual(new Amount(0))
        ->and($charge->getDistributionMethod())->toBeNull()
        ->and($charge->getPayer())->toBeNull();
});

it('builds charges immutably with withers', function () {
    $member = Member::factory()->make();
    $member->id = 1;

    $charge = Charge::create();

    $withAmount = $charge->withAmount(new Amount(1500));
    $withMethod = $withAmount->withDistributionMethod(DistributionMethod::EQUAL);
    $withPayer = $withMethod->withPayer($member);

    expect($withAmount)->not->toBe($charge)
        ->and($withAmount->getAmountOrZero())->toEqual(new Amount(1500))
        ->and($charge->getAmountOrZero())->toEqual(new Amount(0))
        ->and($withMethod->getDistributionMethod())->toBe(DistributionMethod::EQUAL)
        ->and($withMethod->getPayer())->toBeNull()
        ->and($withPayer->getPayer())->toBe($member);
});
