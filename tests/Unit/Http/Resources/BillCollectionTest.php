<?php

namespace Tests\Unit\Http\Resources;

use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Http\Resources\BillResource;
use App\Http\Resources\BillResourceCollection;
use App\Models\Bill;
use App\Models\Member;
use Illuminate\Http\Request;
use Mockery;
use Mockery\MockInterface;
use ReflectionClass;

test('it transforms collection to array with data and meta', function () {
    // Arrange
    // Create mock for first bill
    $member1 = Mockery::mock(Member::class, function (MockInterface $mock) {
        $mock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $mock->shouldReceive('getAttribute')->with('full_name')->andReturn('John Doe');
    });

    $amount1 = new Amount(10000);

    $bill1 = Mockery::mock(Bill::class, function (MockInterface $mock) use ($member1, $amount1) {
        $mock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $mock->shouldReceive('getAttribute')->with('name')->andReturn('Electricity');
        $mock->shouldReceive('getAttribute')->with('amount')->andReturn($amount1);
        $mock->shouldReceive('getAttribute')->with('distribution_method')->andReturn(DistributionMethod::EQUAL);
        $mock->shouldReceive('getAttribute')->with('member')->andReturn($member1);
        $mock->shouldReceive('getAttribute')->with('created_at')->andReturn(now());
        $mock->shouldReceive('getAttribute')->with('updated_at')->andReturn(now());
    });

    // Create mock for second bill
    $member2 = Mockery::mock(Member::class, function (MockInterface $mock) {
        $mock->shouldReceive('getAttribute')->with('id')->andReturn(2);
        $mock->shouldReceive('getAttribute')->with('full_name')->andReturn('Jane Smith');
    });

    $amount2 = new Amount(5000);

    $bill2 = Mockery::mock(Bill::class, function (MockInterface $mock) use ($member2, $amount2) {
        $mock->shouldReceive('getAttribute')->with('id')->andReturn(2);
        $mock->shouldReceive('getAttribute')->with('name')->andReturn('Water');
        $mock->shouldReceive('getAttribute')->with('amount')->andReturn($amount2);
        $mock->shouldReceive('getAttribute')->with('distribution_method')->andReturn(DistributionMethod::PRORATA);
        $mock->shouldReceive('getAttribute')->with('member')->andReturn($member2);
        $mock->shouldReceive('getAttribute')->with('created_at')->andReturn(now());
        $mock->shouldReceive('getAttribute')->with('updated_at')->andReturn(now());
    });

    // Create mocked BillResource instances
    $billResource1 = Mockery::mock(BillResource::class, function (MockInterface $mock) use ($amount1) {
        $mock->shouldReceive('offsetGet')->with('amount')->andReturn($amount1);
    });
    $billResource2 = Mockery::mock(BillResource::class, function (MockInterface $mock) use ($amount2) {
        $mock->shouldReceive('offsetGet')->with('amount')->andReturn($amount2);
    });

    // Create collection with mocked resources
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
    $member = Mockery::mock(Member::class, function (MockInterface $mock) {
        $mock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $mock->shouldReceive('getAttribute')->with('full_name')->andReturn('John Doe');
    });

    $amount = new Amount(10000);

    $bill = Mockery::mock(Bill::class, function (MockInterface $mock) use ($member, $amount) {
        $mock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $mock->shouldReceive('getAttribute')->with('name')->andReturn('Electricity');
        $mock->shouldReceive('getAttribute')->with('amount')->andReturn($amount);
        $mock->shouldReceive('getAttribute')->with('distribution_method')->andReturn(DistributionMethod::EQUAL);
        $mock->shouldReceive('getAttribute')->with('member')->andReturn($member);
        $mock->shouldReceive('getAttribute')->with('created_at')->andReturn(now());
        $mock->shouldReceive('getAttribute')->with('updated_at')->andReturn(now());
    });

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
    // Create mock for first bill
    $member1 = Mockery::mock(Member::class, function (MockInterface $mock) {
        $mock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $mock->shouldReceive('getAttribute')->with('full_name')->andReturn('John Doe');
    });

    $amount1 = new Amount(10000);

    $bill1 = Mockery::mock(Bill::class, function (MockInterface $mock) use ($member1, $amount1) {
        $mock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $mock->shouldReceive('getAttribute')->with('name')->andReturn('Electricity');
        $mock->shouldReceive('getAttribute')->with('amount')->andReturn($amount1);
        $mock->shouldReceive('getAttribute')->with('distribution_method')->andReturn(DistributionMethod::EQUAL);
        $mock->shouldReceive('getAttribute')->with('member')->andReturn($member1);
        $mock->shouldReceive('getAttribute')->with('created_at')->andReturn(now());
        $mock->shouldReceive('getAttribute')->with('updated_at')->andReturn(now());
    });

    // Create mock for second bill
    $member2 = Mockery::mock(Member::class, function (MockInterface $mock) {
        $mock->shouldReceive('getAttribute')->with('id')->andReturn(2);
        $mock->shouldReceive('getAttribute')->with('full_name')->andReturn('Jane Smith');
    });

    $amount2 = new Amount(20000);

    $bill2 = Mockery::mock(Bill::class, function (MockInterface $mock) use ($member2, $amount2) {
        $mock->shouldReceive('getAttribute')->with('id')->andReturn(2);
        $mock->shouldReceive('getAttribute')->with('name')->andReturn('Water');
        $mock->shouldReceive('getAttribute')->with('amount')->andReturn($amount2);
        $mock->shouldReceive('getAttribute')->with('distribution_method')->andReturn(DistributionMethod::PRORATA);
        $mock->shouldReceive('getAttribute')->with('member')->andReturn($member2);
        $mock->shouldReceive('getAttribute')->with('created_at')->andReturn(now());
        $mock->shouldReceive('getAttribute')->with('updated_at')->andReturn(now());
    });

    // Create mocked BillResource instances
    $billResource1 = Mockery::mock(BillResource::class, function (MockInterface $mock) use ($amount1) {
        $mock->shouldReceive('offsetGet')->with('amount')->andReturn($amount1);
    });
    $billResource2 = Mockery::mock(BillResource::class, function (MockInterface $mock) use ($amount2) {
        $mock->shouldReceive('offsetGet')->with('amount')->andReturn($amount2);
    });

    // Create collection with mocked resources
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
    // Create mocks for three bills
    $member = Mockery::mock(Member::class, function (MockInterface $mock) {
        $mock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $mock->shouldReceive('getAttribute')->with('full_name')->andReturn('John Doe');
    });

    $amount = new Amount(10000);

    $bill1 = Mockery::mock(Bill::class, function (MockInterface $mock) use ($member, $amount) {
        $mock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $mock->shouldReceive('getAttribute')->with('name')->andReturn('Electricity');
        $mock->shouldReceive('getAttribute')->with('amount')->andReturn($amount);
        $mock->shouldReceive('getAttribute')->with('distribution_method')->andReturn(DistributionMethod::EQUAL);
        $mock->shouldReceive('getAttribute')->with('member')->andReturn($member);
        $mock->shouldReceive('getAttribute')->with('created_at')->andReturn(now());
        $mock->shouldReceive('getAttribute')->with('updated_at')->andReturn(now());
    });

    $bill2 = Mockery::mock(Bill::class, function (MockInterface $mock) use ($member, $amount) {
        $mock->shouldReceive('getAttribute')->with('id')->andReturn(2);
        $mock->shouldReceive('getAttribute')->with('name')->andReturn('Water');
        $mock->shouldReceive('getAttribute')->with('amount')->andReturn($amount);
        $mock->shouldReceive('getAttribute')->with('distribution_method')->andReturn(DistributionMethod::EQUAL);
        $mock->shouldReceive('getAttribute')->with('member')->andReturn($member);
        $mock->shouldReceive('getAttribute')->with('created_at')->andReturn(now());
        $mock->shouldReceive('getAttribute')->with('updated_at')->andReturn(now());
    });

    $bill3 = Mockery::mock(Bill::class, function (MockInterface $mock) use ($member, $amount) {
        $mock->shouldReceive('getAttribute')->with('id')->andReturn(3);
        $mock->shouldReceive('getAttribute')->with('name')->andReturn('Internet');
        $mock->shouldReceive('getAttribute')->with('amount')->andReturn($amount);
        $mock->shouldReceive('getAttribute')->with('distribution_method')->andReturn(DistributionMethod::EQUAL);
        $mock->shouldReceive('getAttribute')->with('member')->andReturn($member);
        $mock->shouldReceive('getAttribute')->with('created_at')->andReturn(now());
        $mock->shouldReceive('getAttribute')->with('updated_at')->andReturn(now());
    });

    // Create BillResource instances with mocked bills
    $billResource1 = new BillResource($bill1);
    $billResource2 = new BillResource($bill2);
    $billResource3 = new BillResource($bill3);

    // Create collection with mocked resources
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
    // Create mock for first bill
    $member1 = Mockery::mock(Member::class, function (MockInterface $mock) {
        $mock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $mock->shouldReceive('getAttribute')->with('full_name')->andReturn('John Doe');
    });

    $amount1 = new Amount(10000);

    $bill1 = Mockery::mock(Bill::class, function (MockInterface $mock) use ($member1, $amount1) {
        $mock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $mock->shouldReceive('getAttribute')->with('name')->andReturn('Electricity');
        $mock->shouldReceive('getAttribute')->with('amount')->andReturn($amount1);
        $mock->shouldReceive('getAttribute')->with('distribution_method')->andReturn(DistributionMethod::EQUAL);
        $mock->shouldReceive('getAttribute')->with('member')->andReturn($member1);
        $mock->shouldReceive('getAttribute')->with('created_at')->andReturn(now());
        $mock->shouldReceive('getAttribute')->with('updated_at')->andReturn(now());
    });

    // Create mock for second bill
    $member2 = Mockery::mock(Member::class, function (MockInterface $mock) {
        $mock->shouldReceive('getAttribute')->with('id')->andReturn(2);
        $mock->shouldReceive('getAttribute')->with('full_name')->andReturn('Jane Smith');
    });

    $amount2 = new Amount(15000);

    $bill2 = Mockery::mock(Bill::class, function (MockInterface $mock) use ($member2, $amount2) {
        $mock->shouldReceive('getAttribute')->with('id')->andReturn(2);
        $mock->shouldReceive('getAttribute')->with('name')->andReturn('Water');
        $mock->shouldReceive('getAttribute')->with('amount')->andReturn($amount2);
        $mock->shouldReceive('getAttribute')->with('distribution_method')->andReturn(DistributionMethod::PRORATA);
        $mock->shouldReceive('getAttribute')->with('member')->andReturn($member2);
        $mock->shouldReceive('getAttribute')->with('created_at')->andReturn(now());
        $mock->shouldReceive('getAttribute')->with('updated_at')->andReturn(now());
    });

    // Create mocked BillResource instances
    $billResource1 = Mockery::mock(BillResource::class, function (MockInterface $mock) use ($amount1) {
        $mock->shouldReceive('offsetGet')->with('amount')->andReturn($amount1);
    });
    $billResource2 = Mockery::mock(BillResource::class, function (MockInterface $mock) use ($amount2) {
        $mock->shouldReceive('offsetGet')->with('amount')->andReturn($amount2);
    });

    // Create collection with mocked resources
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
        ->and($result->__toString())->toBe('250,00 €');
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
