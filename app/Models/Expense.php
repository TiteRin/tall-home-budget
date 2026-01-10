<?php

namespace App\Models;

use App\Casts\AmountCast;
use App\Enums\DistributionMethod;
use Database\Factories\ExpenseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    /** @use HasFactory<ExpenseFactory> */
    use HasFactory;

    protected $fillable = ['name', 'amount', 'distribution_method', 'member_id', 'expense_tab_id', 'spent_at'];

    protected $casts = [
        'distribution_method' => DistributionMethod::class,
        'amount' => AmountCast::class
    ];
}
