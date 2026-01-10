<?php

namespace App\Domains\Charges;

use App\Domains\ValueObjects\Amount;
use App\Models\Household;
use Illuminate\Support\Carbon;

final class ChargeCalculator
{

    private Carbon $month;

    private function __construct(private Household $household)
    {
    }

    public static function forHousehold(Household $household): self
    {
        return new self($household);
    }

    public function forMonth(string|Carbon $month)
    {
        $this->month = $month instanceof Carbon
            ? $month->copy()->startOfMonth()
            : Carbon::createFromFormat('Y-m', $month)->startOfMonth();
        return $this;
    }

    public function calculate(): Charges
    {

        return new Charges(
            $this->getBillsTotal(),
            $this->getMonthlyExpensesTotal()
        );
    }

    /**
     * @return Amount
     */
    public function getBillsTotal(): Amount
    {
        $total = $this->household->bills->sum(fn($bill) => $bill->amount->toCents());
        return new Amount($total);
    }

    /**
     * @return Amount
     */
    public function getMonthlyExpensesTotal(): Amount
    {
        $total = $this->household->expenseTabs->sum(fn($expenseTab) => $expenseTab->monthlyAmount($this->month)->toCents());
        return new Amount($total);
    }
}
