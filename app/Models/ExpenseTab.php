<?php

namespace App\Models;

use App\Domains\ValueObjects\Amount;
use App\Domains\ValueObjects\MonthlyPeriod;
use Database\Factories\ExpenseTabFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class ExpenseTab extends Model
{
    /** @use HasFactory<ExpenseTabFactory> */
    use HasFactory;

    protected $fillable = ['name', 'household_id', 'member_id', 'period_start_day', 'period_end_day', 'distribution_method'];

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class, 'expense_tab_id');
    }

    public function periodFor(Carbon $date): MonthlyPeriod
    {
        $start = $date->copy()->startOfMonth();

        if ($date->day < $this->period_start_day) {
            $start->subMonth();
        }

        $start->addDays($this->period_start_day - 1);
        $end = $start->copy()->addMonth()->subDay();


        return new MonthlyPeriod($start, $end);
    }

    public function periodForMonth(Carbon $month): MonthlyPeriod
    {
        $start = $month->copy()->startOfMonth()->addDay($this->period_start_day - 1);
        return $this->periodFor($start);
    }

    public function monthlyAmount(Carbon $month): Amount
    {
        $period = $this->periodForMonth($month);
        $total = $this->expenses()->whereBetween('spent_at', [$period->start, $period->end])->sum('amount');
        return new Amount($total);
    }
}
