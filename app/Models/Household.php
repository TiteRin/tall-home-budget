<?php

namespace App\Models;

use App\DistributionMethod;
use Illuminate\Database\Eloquent\Model;

class Household extends Model
{
    protected $fillable = ['name', 'has_joint_account', 'default_distribution_method'];

    protected $casts = [
        'has_joint_account' => 'boolean',
        'default_distribution_method' => DistributionMethod::class,
    ];

    public function members()
    {
        return $this->hasMany(HouseholdMember::class);
    }

    public function getDefaultDistributionMethod(): DistributionMethod
    {
        return $this->default_distribution_method ?? DistributionMethod::EQUAL;
    }
}
