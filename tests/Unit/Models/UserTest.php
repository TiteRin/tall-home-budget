<?php

namespace Tests\Unit\Models;

use App\Models\Household;
use App\Models\Member;
use App\Models\User;

describe('User Model', function () {

    test('user belongs to member', function () {
        $household = Household::factory()->create();
        $member = Member::factory()->create(['household_id' => $household->id]);
        $user = User::factory()->create(['member_id' => $member->id]);

        expect($user->member)->toBeInstanceOf(Member::class)
            ->and($user->member->id)->toBe($member->id);
    });

    test('user can exist without member', function () {
        $user = User::factory()->create(['member_id' => null]);
        expect($user->member)->toBeNull();
    });

    test('deleting a member deletes associated user', function () {
        $household = Household::factory()->create();
        $member = Member::factory()->create(['household_id' => $household->id]);
        $user = User::factory()->create(['member_id' => $member->id]);

        $userId = $user->id;
        $member->delete();

        expect(User::find($userId))->toBeNull();
    });
});

describe('User Factory', function () {

    test('user factory creates user without member by default', function () {
        $user = User::factory()->create();
        expect($user->member_id)->toBeNull();
    });

    test('user factory can create user with member', function () {
        $member = Member::factory()->create();
        $user = User::factory()->create(['member_id' => $member->id]);

        expect($user->member_id)->toBe($member->id)
            ->and($user->member)->toBeInstanceOf(Member::class);
    });
});
