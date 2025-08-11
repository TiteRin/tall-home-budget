<?php

namespace Tests\Unit\Http\Resources;

use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Http\Resources\BillResource;
use App\Http\Resources\BillResourceCollection;
use App\Models\Bill;
use App\Models\Member;
use Illuminate\Http\Request;
use ReflectionClass;

test('it transforms collection to array with data and meta', function () {
    // Arrange
    // First bill
    $member1 = new Member(['first_name' => 'John', 'last_name' => 'Doe']);
    $member1->id = 1;
    $bill1 = new Bill([
        'id' => 1,
        'name' => 'Electricity',
        'amount' => 10000,
        'distribution_method' => DistributionMethod::EQUAL,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $bill1->setRelation('member', $member1);

    // Second bill
    $member2 = new Member(['first_name' => 'Jane', 'last_name' => 'Smith']);
    $member2->id = 2;
    $bill2 = new Bill([
        'id' => 2,
        'name' => 'Water',
        'amount' => 5000,
        'distribution_method' => DistributionMethod::PRORATA,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $bill2->setRelation('member', $member2);

    // Real BillResource instances
    $billResource1 = new BillResource($bill1);
    $billResource2 = new BillResource($bill2);

    // Create collection with resources
    $billCollection = new BillResourceCollection(collect([$billResource1, $billResource2]));

    // Act
    $result = $billCollection->toArray(new Request());

    // Assert
    expect($result)->toHaveKey('data')
        ->and($result)->toHaveKey('meta')
        ->and($result['data'])->toHaveCount(2)
        ->and($result['meta']['total_count'])->toBe(2)
        ->and($result['meta']['total_amount'])->toBe(15000)
        ->and($result['meta']['total_amount_formatted'])->toBe('150,00 €');
});

test('it returns correct data', function () {
    // Arrange
    $member = new Member(['first_name' => 'John', 'last_name' => 'Doe']);
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

    $billResource = new BillResource($bill);
    $billCollection = new BillResourceCollection(collect([$billResource]));

    // Act
    $result = $billCollection->getData();

    // Assert
    expect($result)->toHaveCount(1)
        ->and($result->first())->toBe($billResource);
});

test('it returns correct meta information', function () {
    // Arrange
    // First bill
    $member1 = new Member(['first_name' => 'John', 'last_name' => 'Doe']);
    $member1->id = 1;
    $bill1 = new Bill([
        'id' => 1,
        'name' => 'Electricity',
        'amount' => 10000,
        'distribution_method' => DistributionMethod::EQUAL,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $bill1->setRelation('member', $member1);

    // Second bill
    $member2 = new Member(['first_name' => 'Jane', 'last_name' => 'Smith']);
    $member2->id = 2;
    $bill2 = new Bill([
        'id' => 2,
        'name' => 'Water',
        'amount' => 20000,
        'distribution_method' => DistributionMethod::PRORATA,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $bill2->setRelation('member', $member2);

    // Real BillResource instances
    $billResource1 = new BillResource($bill1);
    $billResource2 = new BillResource($bill2);

    // Create collection with resources
    $billCollection = new BillResourceCollection(collect([$billResource1, $billResource2]));

    // Act
    $meta = $billCollection->getMeta();

    // Assert
    expect($meta['total_count'])->toBe(2)
        ->and($meta['total_amount'])->toBe(30000)
        ->and($meta['total_amount_formatted'])->toBe('300,00 €');
});

test('it calculates correct length', function () {
    // Arrange
    $member = new Member(['first_name' => 'John', 'last_name' => 'Doe']);
    $member->id = 1;
    $bill1 = new Bill(['id' => 1, 'name' => 'Electricity', 'amount' => 10000, 'distribution_method' => DistributionMethod::EQUAL, 'created_at' => now(), 'updated_at' => now()]);
    $bill2 = new Bill(['id' => 2, 'name' => 'Water', 'amount' => 10000, 'distribution_method' => DistributionMethod::EQUAL, 'created_at' => now(), 'updated_at' => now()]);
    $bill3 = new Bill(['id' => 3, 'name' => 'Internet', 'amount' => 10000, 'distribution_method' => DistributionMethod::EQUAL, 'created_at' => now(), 'updated_at' => now()]);
    $bill1->setRelation('member', $member);
    $bill2->setRelation('member', $member);
    $bill3->setRelation('member', $member);

    $billResource1 = new BillResource($bill1);
    $billResource2 = new BillResource($bill2);
    $billResource3 = new BillResource($bill3);

    $billCollection = new BillResourceCollection(collect([$billResource1, $billResource2, $billResource3]));

    // Use reflection to access protected method
    $reflection = new ReflectionClass($billCollection);
    $method = $reflection->getMethod('length');
    $method->setAccessible(true);

    // Act
    $result = $method->invoke($billCollection);

    // Assert
    expect($result)->toBe(3);
});

test('it calculates correct total amount', function () {
    // Arrange
    // First bill
    $member1 = new Member(['first_name' => 'John', 'last_name' => 'Doe']);
    $member1->id = 1;
    $bill1 = new Bill([
        'id' => 1,
        'name' => 'Electricity',
        'amount' => 10000,
        'distribution_method' => DistributionMethod::EQUAL,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $bill1->setRelation('member', $member1);

    // Second bill
    $member2 = new Member(['first_name' => 'Jane', 'last_name' => 'Smith']);
    $member2->id = 2;
    $bill2 = new Bill([
        'id' => 2,
        'name' => 'Water',
        'amount' => 15000,
        'distribution_method' => DistributionMethod::PRORATA,
        'created_at' => now(),
        'updated_at' => now(),
    ]);
    $bill2->setRelation('member', $member2);

    // Real BillResource instances
    $billResource1 = new BillResource($bill1);
    $billResource2 = new BillResource($bill2);

    // Create collection with resources
    $billCollection = new BillResourceCollection(collect([$billResource1, $billResource2]));

    // Use reflection to access protected method
    $reflection = new ReflectionClass($billCollection);
    $method = $reflection->getMethod('totalAmount');
    $method->setAccessible(true);

    // Act
    $result = $method->invoke($billCollection);

    // Assert
    expect($result)->toBeInstanceOf(Amount::class)
        ->and($result->value())->toBe(25000)
        ->and($result->toCurrency())->toBe('250,00 €');
});

test('it handles empty collection', function () {
    // Arrange - Create an empty collection
    $billCollection = new BillResourceCollection(collect([]));

    // Act
    $result = $billCollection->toArray(new Request());

    // Assert
    expect($result)->toHaveKey('data')
        ->and($result)->toHaveKey('meta')
        ->and($result['data'])->toHaveCount(0)
        ->and($result['meta']['total_count'])->toBe(0)
        ->and($result['meta']['total_amount'])->toBe(0)
        ->and($result['meta']['total_amount_formatted'])->toBe('0,00 €');
});
