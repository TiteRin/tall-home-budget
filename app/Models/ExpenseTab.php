<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseTab extends Model
{
    /** @use HasFactory<\Database\Factories\ExpenseTabFactory> */
    use HasFactory;

    protected $fillable = ['name', 'household_id', 'member_id', 'period_start_day', 'period_end_day', 'distribution_method'];
}
