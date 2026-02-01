<?php

namespace App\Models;

use Database\Factories\ExpenseTabFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExpenseTab extends Model
{
    /** @use HasFactory<ExpenseTabFactory> */
    use HasFactory;

    protected $fillable = ['name', 'from_day', 'household_id'];

    public function household(): BelongsTo
    {
        return $this->belongsTo(Household::class, 'household_id');
    }
}
