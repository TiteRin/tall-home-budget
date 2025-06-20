<?php

namespace App\Models;

use App\DistributionMethod;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Bill extends Model
{
    /** @use HasFactory<\Database\Factories\BillFactory> */
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
    ];

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }
}

