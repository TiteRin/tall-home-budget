<?php

namespace Tests\Feature\Services\Balance;

use App\Domains\ValueObjects\Amount;
use App\Domains\ValueObjects\Balance;
use App\Models\Member;
use App\Services\Movement\BalancesCollection;

it("should initialize with empty array", function () {
    $collection = new BalancesCollection();
    expect($collection)->toBeEmpty();
});

it("getCreditors should return a BalancesCollection", function () {
    $collection = new BalancesCollection([
            new Balance(Member::factory()->create(), new Amount(20000)),
            new Balance(Member::factory()->create(), new Amount(-500)),
            new Balance(Member::factory()->create(), new Amount(-5000)),
        ]
    );

    expect($collection->getCreditors())->toBeInstanceOf(BalancesCollection::class)
        ->and($collection->getCreditors())->toHaveCount(1);
});

it("getDebitors should return a BalancesCollection", function () {
    $collection = new BalancesCollection([
            new Balance(Member::factory()->create(), new Amount(20000)),
            new Balance(Member::factory()->create(), new Amount(-500)),
            new Balance(Member::factory()->create(), new Amount(-5000)),
        ]
    );

    expect($collection->getDebitors())->toBeInstanceOf(BalancesCollection::class)
        ->and($collection->getDebitors())->toHaveCount(2);
});


// Todo more tests
