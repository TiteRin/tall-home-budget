<?php

namespace Tests\Unit\Http\Resources;

use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Http\Resources\BillResource;
use App\Models\Bill;
use App\Models\Member;
use Illuminate\Http\Request;

test('it transforms bill to array with correct structure', function () {
    // Arrange
    $member = new Member([
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);
    $member->id = 1;

    $bill = new Bill([
        'id' => 1,
        'name' => 'Electricity',
        'amount' => 10000, // cents; cast returns Amount
        'distribution_method' => DistributionMethod::EQUAL,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $bill->setRelation('member', $member);

    $resource = new BillResource($bill);

    // Act
    $result = $resource->toArray(new Request());

    // Assert
    expect($result['id'])->toBe($bill->id)
        ->and($result['name'])->toBe('Electricity')
        ->and($result['amount'])->toBeInstanceOf(Amount::class)
        ->and($result['amount']->value())->toBe(10000)
        ->and($result['amount_formatted'])->toBe('100,00 €')
        ->and($result['distribution_method'])->toBe(DistributionMethod::EQUAL->value)
        ->and($result['distribution_method_label'])->toBe(DistributionMethod::EQUAL->label())
        ->and($result['member']['id'])->toBe($member->id)
        ->and($result['member']['full_name'])->toBe('John Doe')
        ->and($result)->toHaveKey('created_at')
        ->and($result)->toHaveKey('updated_at');
});

test('it formats amount correctly', function () {
    // Arrange
    $member = new Member([
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);
    $member->id = 1;

    $bill = new Bill([
        'id' => 1,
        'name' => 'Electricity',
        'amount' => 12345,
        'distribution_method' => DistributionMethod::EQUAL,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $bill->setRelation('member', $member);

    $resource = new BillResource($bill);

    // Act
    $result = $resource->toArray(new Request());

    // Assert
    expect($result['amount']->value())->toBe(12345)
        ->and($result['amount_formatted'])->toBe('123,45 €');
});

test('it handles different distribution methods', function () {
    // Arrange - Test with PRORATA distribution method
    $member = new Member([
        'first_name' => 'John',
        'last_name' => 'Doe',
    ]);
    $member->id = 1;

    $bill = new Bill([
        'id' => 1,
        'name' => 'Electricity',
        'amount' => 10000,
        'distribution_method' => DistributionMethod::PRORATA,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $bill->setRelation('member', $member);

    $resource = new BillResource($bill);

    // Act
    $result = $resource->toArray(new Request());

    // Assert
    expect($result['distribution_method'])->toBe(DistributionMethod::PRORATA->value)
        ->and($result['distribution_method_label'])->toBe(DistributionMethod::PRORATA->label());

    // Arrange - Test with EQUAL distribution method
    $bill = new Bill([
        'id' => 2,
        'name' => 'Water',
        'amount' => 10000,
        'distribution_method' => DistributionMethod::EQUAL,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $bill->setRelation('member', $member);

    $resource = new BillResource($bill);

    // Act
    $result = $resource->toArray(new Request());

    // Assert
    expect($result['distribution_method'])->toBe(DistributionMethod::EQUAL->value)
        ->and($result['distribution_method_label'])->toBe(DistributionMethod::EQUAL->label());
});

test('it includes member information', function () {
    // Arrange
    $member = new Member([
        'first_name' => 'Jane',
        'last_name' => 'Smith',
    ]);
    $member->id = 1;

    $bill = new Bill([
        'id' => 1,
        'name' => 'Electricity',
        'amount' => 10000,
        'distribution_method' => DistributionMethod::EQUAL,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $bill->setRelation('member', $member);

    $resource = new BillResource($bill);

    // Act
    $result = $resource->toArray(new Request());

    // Assert
    expect($result)->toHaveKey('member')
        ->and($result['member']['id'])->toBe(1)
        ->and($result['member']['full_name'])->toBe('Jane Smith');
});
