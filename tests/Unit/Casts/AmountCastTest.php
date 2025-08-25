<?php

namespace Tests\Unit\Casts;

use App\Casts\AmountCast;
use App\Domains\ValueObjects\Amount;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class FakeModel extends Model
{
    public string $amount;
}

test('should cast a raw integer value to an Amount object', function () {
    $cast = new AmountCast();
    $model = new FakeModel();

    $amount = $cast->get($model, 'amount', 1234, []);
    expect($amount)
        ->toBeInstanceOf(Amount::class)
        ->and($amount->value())->toBe(1234);
});

test('should cast an Amount object back from a raw integer', function () {
    $cast = new AmountCast();
    $model = new FakeModel();

    $raw = $cast->set($model, 'amount', 1234, []);
    expect($raw)->toBe(1234);
});

test('should throw an exception when casting an invalid value', function () {
    $cast = new AmountCast();
    $model = new FakeModel();

    $raw = $cast->get($model, 'amount', 'invalid', []);
})->throws(InvalidArgumentException::class);

test('when value is null, AmountCast should return null', function () {
    $cast = new AmountCast();
    $model = new FakeModel();

    expect($cast->get($model, 'amount', null, []))->toBeNull()
        ->and($cast->set($model, 'amount', null, []))->toBeNull();
});
