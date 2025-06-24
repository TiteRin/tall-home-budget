<?php

namespace App\Models;

use App\Enums\DistributionMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Household extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'has_joint_account', 'default_distribution_method'];

    protected $casts = [
        'has_joint_account' => 'boolean',
        'default_distribution_method' => DistributionMethod::class,
    ];

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function getDefaultDistributionMethod(): DistributionMethod
    {
        return $this->default_distribution_method ?? DistributionMethod::EQUAL;
    }
}
