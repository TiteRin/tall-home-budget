<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HouseholdMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'household_id', 
        'first_name', 
        'last_name'
    ];

    public function household()
    {
        return $this->belongsTo(Household::class);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
