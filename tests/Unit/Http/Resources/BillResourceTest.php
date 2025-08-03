<?php

namespace Tests\Unit\Http\Resources;

use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Http\Resources\BillResource;
use App\Models\Bill;
use App\Models\Member;
use Illuminate\Http\Request;
use Mockery;
use Mockery\MockInterface;

test('it transforms bill to array with correct structure', function () {
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
    $member = Mockery::mock(Member::class, function (MockInterface $mock) {
        $mock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $mock->shouldReceive('getAttribute')->with('full_name')->andReturn('John Doe');
    });

    $amount = new Amount(12345);

    $bill = Mockery::mock(Bill::class, function (MockInterface $mock) use ($member, $amount) {
        $mock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $mock->shouldReceive('getAttribute')->with('name')->andReturn('Electricity');
        $mock->shouldReceive('getAttribute')->with('amount')->andReturn($amount);
        $mock->shouldReceive('getAttribute')->with('distribution_method')->andReturn(DistributionMethod::EQUAL);
        $mock->shouldReceive('getAttribute')->with('member')->andReturn($member);
        $mock->shouldReceive('getAttribute')->with('created_at')->andReturn(now());
        $mock->shouldReceive('getAttribute')->with('updated_at')->andReturn(now());
    });

    $resource = new BillResource($bill);

    // Act
    $result = $resource->toArray(new Request());

    // Assert
    expect($result['amount']->value())->toBe(12345)
        ->and($result['amount_formatted'])->toBe('123,45 €');
});

test('it handles different distribution methods', function () {
    // Arrange - Test with PRORATA distribution method
    $member = Mockery::mock(Member::class, function (MockInterface $mock) {
        $mock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $mock->shouldReceive('getAttribute')->with('full_name')->andReturn('John Doe');
    });

    $amount = new Amount(10000);

    $bill = Mockery::mock(Bill::class, function (MockInterface $mock) use ($member, $amount) {
        $mock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $mock->shouldReceive('getAttribute')->with('name')->andReturn('Electricity');
        $mock->shouldReceive('getAttribute')->with('amount')->andReturn($amount);
        $mock->shouldReceive('getAttribute')->with('distribution_method')->andReturn(DistributionMethod::PRORATA);
        $mock->shouldReceive('getAttribute')->with('member')->andReturn($member);
        $mock->shouldReceive('getAttribute')->with('created_at')->andReturn(now());
        $mock->shouldReceive('getAttribute')->with('updated_at')->andReturn(now());
    });

    $resource = new BillResource($bill);

    // Act
    $result = $resource->toArray(new Request());

    // Assert
    expect($result['distribution_method'])->toBe(DistributionMethod::PRORATA->value)
        ->and($result['distribution_method_label'])->toBe(DistributionMethod::PRORATA->label());

    // Arrange - Test with EQUAL distribution method
    $bill = Mockery::mock(Bill::class, function (MockInterface $mock) use ($member, $amount) {
        $mock->shouldReceive('getAttribute')->with('id')->andReturn(2);
        $mock->shouldReceive('getAttribute')->with('name')->andReturn('Water');
        $mock->shouldReceive('getAttribute')->with('amount')->andReturn($amount);
        $mock->shouldReceive('getAttribute')->with('distribution_method')->andReturn(DistributionMethod::EQUAL);
        $mock->shouldReceive('getAttribute')->with('member')->andReturn($member);
        $mock->shouldReceive('getAttribute')->with('created_at')->andReturn(now());
        $mock->shouldReceive('getAttribute')->with('updated_at')->andReturn(now());
    });

    $resource = new BillResource($bill);

    // Act
    $result = $resource->toArray(new Request());

    // Assert
    expect($result['distribution_method'])->toBe(DistributionMethod::EQUAL->value)
        ->and($result['distribution_method_label'])->toBe(DistributionMethod::EQUAL->label());
});

test('it includes member information', function () {
    // Arrange
    $member = Mockery::mock(Member::class, function (MockInterface $mock) {
        $mock->shouldReceive('getAttribute')->with('id')->andReturn(1);
        $mock->shouldReceive('getAttribute')->with('full_name')->andReturn('Jane Smith');
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

    $resource = new BillResource($bill);

    // Act
    $result = $resource->toArray(new Request());

    // Assert
    expect($result)->toHaveKey('member')
        ->and($result['member']['id'])->toBe(1)
        ->and($result['member']['full_name'])->toBe('Jane Smith');
});
