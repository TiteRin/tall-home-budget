<?php

namespace App\Models;

use App\Enums\DistributionMethod;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use App\Exceptions\MismatchedHouseholdException;
use App\Traits\HasCurrencyFormatting;

class Bill extends Model
{
    /** @use HasFactory<\Database\Factories\BillFactory> */
    use HasFactory, HasCurrencyFormatting;

    protected $fillable = [
        'name',
        'amount',
        'distribution_method',
        'household_id',
        'member_id',
    ];

    protected $casts = [
        'distribution_method' => DistributionMethod::class,
    ];

    protected static function booted(): void 
    {
        static::creating(function (Bill $bill) {

            if (is_null($bill->member_id) || is_null($bill->household_id)) {
                return;
            }

            $member = Member::find($bill->member_id);

            if ($member->household_id !== $bill->household_id) {
                throw new MismatchedHouseholdException();
            }
        });
    }

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function getAmountFormattedAttribute(): string
    {
        return $this->formatCurrency($this->amount);
    }
}

