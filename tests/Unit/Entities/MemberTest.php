<?php

namespace Tests\Unit\Entities;

use App\Domains\Entities\JointAccount;
use App\Models\Member;

it("should differentiate between a household’s member and a joint account", function () {

    $alice = new Member(['first_name' => 'Alice', 'last_name' => 'Doe']);
    $joint = new JointAccount();

    expect($alice->isJoint())->toBeFalse();
    expect($joint->isJoint())->toBeTrue();
});
