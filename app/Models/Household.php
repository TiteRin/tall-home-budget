<?php

namespace App\Models;

use App\Domains\ValueObjects\Amount;
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

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    public function getTotalAmountAttribute(): Amount
    {
        return new Amount($this->bills->sum(fn($bill) => $bill->amount->value()) ?? 0);
    }

    public function getTotalAmountFormattedAttribute(): string
    {
        return $this->total_amount->toCurrency();
    }

    public function getDefaultDistributionMethod(): DistributionMethod
    {
        return $this->default_distribution_method ?? DistributionMethod::EQUAL;
    }

    public function hasJointAccount(): bool
    {
        return $this->has_joint_account;
    }
}
