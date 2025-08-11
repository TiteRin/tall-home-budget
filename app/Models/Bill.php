<?php

namespace App\Models;

use App\Casts\AmountCast;
use App\Enums\DistributionMethod;
use App\Exceptions\MismatchedHouseholdException;
use Database\Factories\BillFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use InvalidArgumentException;

class Bill extends Model
{
    /** @use HasFactory<BillFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'amount',
        'distribution_method',
        'household_id',
        'member_id',
    ];

    protected $casts = [
        'distribution_method' => DistributionMethod::class,
        'amount' => AmountCast::class
    ];

    protected static function booted(): void
    {
        static::creating(function (Bill $bill) {

            if (is_null($bill->name)) {
                throw new InvalidArgumentException("name is required");
            }

            if (is_null($bill->amount)) {
                throw new InvalidArgumentException("amount is required");
            }

            if (is_null($bill->household_id)) {
                throw new InvalidArgumentException("household_id is required");
            }

            if (is_null($bill->distribution_method)) {
                throw new InvalidArgumentException("distribution_method is required");
            }

            if (is_null($bill->member_id)) {
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
}

