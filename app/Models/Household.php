<?php

namespace App\Models;

use App\Domains\Entities\JointAccount;
use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Household extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'has_joint_account', 'default_distribution_method', 'onboarding_configured_household', 'onboarding_added_bills'];

    protected $casts = [
        'has_joint_account' => 'boolean',
        'default_distribution_method' => DistributionMethod::class,
        'onboarding_configured_household' => 'boolean',
        'onboarding_added_bills' => 'boolean',
    ];

    public function members(): HasMany
    {
        return $this->hasMany(Member::class, 'household_id');
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class, 'household_id');
    }

    public function expenseTabs(): HasMany
    {
        return $this->hasMany(ExpenseTab::class, 'household_id');
    }

    public function expenses(): HasManyThrough
    {
        return $this->hasManyThrough(Expense::class, ExpenseTab::class, 'household_id', 'expense_tab_id', 'id', 'id');
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

    public function jointAccount(): ?JointAccount
    {
        if (!$this->has_joint_account) {
            return null;
        }

        return new JointAccount(
            [
                'first_name' => 'Compte joint',
                'household_id' => $this->id
            ]
        );
    }
}
