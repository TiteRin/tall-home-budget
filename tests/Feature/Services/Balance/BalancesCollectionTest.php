<?php

namespace Tests\Feature\Services\Balance;

use App\Services\Movement\BalancesCollection;

it("should initialize with empty array", function () {
    $collection = new BalancesCollection();
    expect($collection)->toBeEmpty();
});

it("getCreditors should return a BalancesCollection", function () {
});

it("getDebitors should return a BalancesCollection", function () {
});


// Todo more tests
