<?php

use App\Domains\ValueObjects\Amount;
use App\Domains\ValueObjects\Charge;
use App\Domains\ValueObjects\ChargesCollection;
use App\Enums\DistributionMethod;
use App\Models\Member;

it('returns zero total for an empty collection', function () {
    $collection = new ChargesCollection();

    expect($collection->getTotalAmount())->toEqual(new Amount(0));
});

it('calculates totals by member, joint account, and distribution method', function () {
    $memberA = Member::factory()->make();
    $memberA->id = 1;
    $memberB = Member::factory()->make();
    $memberB->id = 2;

    $collection = new ChargesCollection([
        Charge::create()
            ->withAmount(new Amount(1000))
            ->withPayer($memberA)
            ->withDistributionMethod(DistributionMethod::EQUAL),
        Charge::create()
            ->withAmount(new Amount(2500))
            ->withPayer($memberB)
            ->withDistributionMethod(DistributionMethod::PRORATA),
        Charge::create()
            ->withAmount(new Amount(500))
            ->withPayer(null)
            ->withDistributionMethod(DistributionMethod::EQUAL),
        Charge::create()->withPayer(null),
    ]);

    expect($collection->getTotalAmount())->toEqual(new Amount(4000))
        ->and($collection->getTotalAmountForMember($memberA))->toEqual(new Amount(1000))
        ->and($collection->getTotalAmountForMemberId($memberB->id))->toEqual(new Amount(2500))
        ->and($collection->getTotalAmountForJointAccount())->toEqual(new Amount(500))
        ->and($collection->getTotalAmountForDistributionMethod(DistributionMethod::EQUAL))->toEqual(new Amount(1500));
});
