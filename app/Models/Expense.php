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

    protected $fillable = [
        'name',
        'spent_on',
        'amount',
        'distribution_method',
        'expense_tab_id',
        'member_id',
    ];

    protected $casts = [
        'distribution_method' => DistributionMethod::class,
        'amount' => AmountCast::class,
        'spent_on' => 'immutable_datetime'
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function expenseTab()
    {
        return $this->belongsTo(ExpenseTab::class);
    }
}
