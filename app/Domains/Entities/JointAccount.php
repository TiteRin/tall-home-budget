<?php

namespace App\Domains\Entities;

use App\Models\Member;

class JointAccount extends Member
{
    public function isJoint(): bool
    {
        return true;
    }
}
