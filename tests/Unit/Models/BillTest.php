<?php

namespace Tests\Unit\Models;

use App\Models\Bill;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('can get the formatted amount', function () {
    $bill = new Bill();
    $bill->amount = 17900;
    expect($bill->formatted_amount)->toBe('179,00 €');
});

test('returns formatted amount for zero value', function()
{
    $bill = new Bill();
    $bill->amount = 0;
    expect($bill->formatted_amount)->toBe('0,00 €');
});

test('returns formatted amount for negative value', function()
{
   $bill = new Bill();
   $bill->amount = -10000;
   expect($bill->formatted_amount)->toBe('-100,00 €');
});
