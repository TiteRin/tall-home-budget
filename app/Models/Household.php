<?php

namespace App\Models;

use App\Enums\DistributionMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Traits\HasCurrencyFormatting;

class Household extends Model
{
    use HasFactory, HasCurrencyFormatting;

    protected $fillable = ['name', 'has_joint_account', 'default_distribution_method'];

    protected $casts = [
        'has_joint_account' => 'boolean',
        'default_distribution_method' => DistributionMethod::class,
    ];

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    public function getTotalAmountAttribute(): int
    {
        return $this->bills->sum('amount') ?? 0;
    }

    public function getTotalAmountFormattedAttribute(): string
    {
        return $this->formatCurrency($this->total_amount);
    }

    public function getDefaultDistributionMethod(): DistributionMethod
    {
        return $this->default_distribution_method ?? DistributionMethod::EQUAL;
    }
}
