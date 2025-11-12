<?php

namespace Tests\Unit\Models;

use App\Models\Household;
use App\Models\Member;
use App\Models\User;

describe('Member Model', function () {
    test('member can have a user', function () {
        $household = Household::factory()->create();
        $member = Member::factory()->create(['household_id' => $household->id]);
        $user = User::factory()->create(['member_id' => $member->id]);

        expect($member->user)->toBeInstanceOf(User::class)
            ->and($member->user->id)->toBe($user->id);
    });

    test('member can exist without user', function () {
        $member = Member::factory()->create();
        expect($member->user)->toBeNull();
    });

    test('member knows if it has a user account', function () {
        $househod = Household::factory()->create();
        $memberWithUser = Member::factory()->create(['household_id' => $househod->id]);
        User::factory()->create(['member_id' => $memberWithUser->id]);

        $memberWithoutUser = Member::factory()->create(['household_id' => $househod->id]);

        expect($memberWithUser->hasUserAccount())->toBeTrue()
            ->and($memberWithoutUser->hasUserAccount())->toBeFalse();
    });
});
