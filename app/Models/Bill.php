<?php

namespace App\Models;

use App\DistributionMethod;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    /** @use HasFactory<\Database\Factories\BillFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'amount',
        'distribution_method',
        'household_id',
        'household_member_id',
    ];

    protected $casts = [
        'distribution_method' => DistributionMethod::class,
    ];

    public function household()
    {
        return $this->belongsTo(Household::class);
    }

    public function householdMember()
    {
        return $this->belongsTo(HouseholdMember::class);
    }
}

