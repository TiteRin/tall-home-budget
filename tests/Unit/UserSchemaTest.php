<?php

namespace Tests\Unit;

use App\Models\Member;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Schema;

describe('UserSchema', function () {
    test('users table does not have name column', function () {
        expect(Schema::hasColumn('users', 'name'))->toBeFalse();
    });

    test('users table has email column', function () {
        expect(Schema::hasColumn('users', 'email'))->toBeTrue();
    });

    test('users table has member_id column', function () {
        expect(Schema::hasColumn('users', 'member_id'))->toBeTrue();
    });

    test('member_id is nullable', function () {
        $user = User::factory()->create(['member_id' => null]);
        expect($user->member_id)->toBeNull();
    });

    test('member_id is unique when not null', function () {
        $memberJohn = Member::factory()->create(['first_name' => 'John']);

        $userJohn = User::factory()->create(['member_id' => $memberJohn->id]);
        expect($userJohn->member_id)->toBe($memberJohn->id)
            ->and(
                fn() => User::factory()->create(['member_id' => $memberJohn->id])
            )->toThrow(Exception::class);

    });
});
