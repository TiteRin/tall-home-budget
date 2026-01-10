<?php

namespace App\Models;

use App\Domains\ValueObjects\MonthlyPeriod;
use Database\Factories\ExpenseTabFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class ExpenseTab extends Model
{
    /** @use HasFactory<ExpenseTabFactory> */
    use HasFactory;

    protected $fillable = ['name', 'household_id', 'member_id', 'period_start_day', 'period_end_day', 'distribution_method'];

    public function periodFor(Carbon $date): MonthlyPeriod
    {
        $start = $date->startOfMonth()->addDays($this->period_start_day - 1);
        $end = $start->copy()->addMonth()->subDay();
        return new MonthlyPeriod($start, $end);
    }
}
