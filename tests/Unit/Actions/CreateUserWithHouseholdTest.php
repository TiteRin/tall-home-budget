<?php

namespace Tests\Unit\Actions;

use App\Actions\Users\CreateUserWithHousehold;
use App\Enums\DistributionMethod;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

describe('CreateUserWithHousehold', function () {

    test('can create user with household and member', function () {
        $action = new CreateUserWithHousehold();

        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123!',
            'household_name' => 'Doe Family',
            'default_distribution_method' => DistributionMethod::EQUAL->value,
            'has_joint_account' => false
        ];

        $user = $action->execute($userData);
        expect($user)->toBeInstanceOf(User::class)
            ->and($user->email)->toBe('john@example.com')
            ->and($user->member)->not->toBeNull()
            ->and($user->member->first_name)->toBe('John')
            ->and($user->member->last_name)->toBe('Doe')
            ->and($user->member->household)->not->toBeNull()
            ->and($user->member->household->name)->toBe('Doe Family');
    });

    test('password is hashed', function () {
        $action = new CreateUserWithHousehold();

        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'password123!',
            'household_name' => 'Doe Family',
            'default_distribution_method' => DistributionMethod::EQUAL->value,
            'has_joint_account' => false
        ];

        $user = $action->execute($userData);
        expect(Hash::check('password123!', $user->password))->toBeTrue();
    });

    test('email must be unique', function () {

        $user = User::factory()->create(['email' => 'duplicate@example.com']);
        $action = new CreateUserWithHousehold();

        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'duplicate@example.com',
            'password' => 'password123!',
            'household_name' => 'Doe Family',
            'default_distribution_method' => DistributionMethod::EQUAL->value,
            'has_joint_account' => false
        ];

        $action->execute($userData);

    })->throws(ValidationException::class);

    test('household has one member after creation', function () {
        $action = new CreateUserWithHousehold();

        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'duplicate@example.com',
            'password' => 'password123!',
            'household_name' => 'Doe Family',
            'default_distribution_method' => DistributionMethod::EQUAL->value,
            'has_joint_account' => false
        ];

        $user = $action->execute($userData);
        expect($user->member->household->members()->count())->toBeOne();
    });

    test('validates required fields', function () {
        $action = new CreateUserWithHousehold();
        $action->execute([]);
    })->throws(ValidationException::class);

    test('validates email format', function () {
        $action = new CreateUserWithHousehold();
        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'invalid-format',
            'password' => 'password123!',
            'household_name' => 'Doe Family',
            'default_distribution_method' => DistributionMethod::EQUAL->value,
            'has_joint_account' => false
        ];
        $action->execute($userData);
    })->throws(ValidationException::class);

    test('validates password minimum length', function () {
        $action = new CreateUserWithHousehold();
        $userData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john@example.com',
            'password' => 'abc',
            'household_name' => 'Doe Family',
            'default_distribution_method' => DistributionMethod::EQUAL->value,
            'has_joint_account' => false
        ];
        $action->execute($userData);
    })->throws(ValidationException::class);
});
