<?php

namespace App\Models;

use App\Domains\ValueObjects\Amount;
use App\Services\Expense\ExpenseCollection;
use App\Services\Expense\ExpenseServiceResolver;
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

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function totalAmount(): Amount
    {
        return ExpenseCollection::from($this->expenses)->getTotal();
    }


    public function totalAmountForCurrentPeriod(): Amount
    {
        $resolver = new ExpenseServiceResolver($this->from_day);
        $currentMonthlyPeriod = $resolver->getCurrentMonthlyPeriod();

        return ExpenseCollection::from($this->expenses)
            ->forMonthlyPeriod($currentMonthlyPeriod)
            ->getTotal();
    }
}
