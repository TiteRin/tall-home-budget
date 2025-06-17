<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Household extends Model
{
    protected $fillable = ['name', 'has_joint_account'];

    public function members()
    {
        return $this->hasMany(HouseholdMember::class);
    }
}
