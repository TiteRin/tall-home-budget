<?php

use App\Domains\ValueObjects\Amount;

it('serializes Amount to array payload for Livewire', function () {
    $amount = new Amount(1234);

    expect($amount->toLivewire())
        ->toBeArray()
        ->toMatchArray(['cents' => 1234]);
});

it('restores Amount from Livewire payloads', function () {
    expect(Amount::fromLivewire(1234))->toBeInstanceOf(Amount::class)
        ->and(Amount::fromLivewire(1234)->toCents())->toBe(1234)
        ->and(Amount::fromLivewire('5678')->toCents())->toBe(5678)
        ->and(Amount::fromLivewire(['cents' => '9012'])->toCents())->toBe(9012);
});
