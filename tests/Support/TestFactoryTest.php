<?php

namespace Tests\Support;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a user with a member and a household', function () {
    $factory = test_factory()
        ->withHousehold()
        ->withMember()
        ->withUser();

    $user = $factory->user();

    expect($user)
        ->toBeInstanceOf(User::class)
        ->and($user->member)->not->toBeNull()
        ->and($user->member->household)->not->toBeNull();
});

it('is immutable and does not mutate previous instance', function () {
    $base = test_factory();

    $withHousehold = $base->withHousehold();
    $withMember = $withHousehold->withMember();

    expect($base->household())->toBeNull()
        ->and($withHousehold->household())->not->toBeNull()
        ->and($withHousehold->member())->toBeNull()
        ->and($withMember->member())->not->toBeNull();
});
