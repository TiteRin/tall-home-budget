<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\Schema;

describe('UserSchema', function () {
    test('users table does not have name column', function () {
        expect(Schema::hasColumn('users', 'name'))->toBeFalse();
    });

    test('users table has email column', function () {
        expect(Schema::hasColumn('users', 'email'))->toBeTrue();
    });
});
