<?php

namespace App\Services\Movement;

use App\Domains\ValueObjects\Amount;
use App\Models\Expense;
use App\Models\Member;
use App\Services\Bill\BillsCollection;
use App\Services\Expense\ExpenseCollection;
use Exception;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class MovementsService
{
    private array $movements;

    private function __construct(
        protected ?Collection        $members = new Collection(),
        protected ?BillsCollection   $bills = new BillsCollection(),
        protected ?ExpenseCollection $expenses = new ExpenseCollection(),
        protected ?array             $incomes = []
    )
    {
        ;
    }

    public function withMembers(Collection $members)
    {
        return new self(
            $members,
            $this->bills,
            $this->expenses,
            $this->incomes
        );
    }

    public function withBills(BillsCollection $bills)
    {
        return new self(
            $this->members,
            $bills,
            $this->expenses,
            $this->incomes
        );
    }

    public function withExpenses(ExpenseCollection $expenses)
    {
        return new self(
            $this->members,
            $this->bills,
            $expenses,
            $this->incomes
        );
    }

    public function addExpenses(ExpenseCollection $expenses)
    {
        return new self(
            $this->members,
            $this->bills,
            $this->expenses->merge($expenses),
            $this->incomes
        );
    }

    public function addExpense(Expense $expense)
    {
        return new self(
            $this->members,
            $this->bills,
            $this->expenses->add($expense),
            $this->incomes
        );
    }

    public function hasMembers(): bool
    {
        return count($this->members) > 0;
    }

    public function hasBills(): bool
    {
        return count($this->bills) > 0;
    }

    public function hasExpenses(): bool
    {
        return count($this->expenses) > 0;
    }

    public function setIncomeFor(Member $member, Amount $amount)
    {
        if (!$this->members->contains('id', $member->id)) {
            throw new InvalidArgumentException();
        }

        $currentIncomes = $this->incomes;
        $currentIncomes[$member->id] = $amount;

        return new self(
            $this->members,
            $this->bills,
            $this->expenses,
            $currentIncomes
        );
    }

    public function removeIncomeFor(Member $member)
    {
        if (!$this->members->contains('id', $member->id)) {
            throw new InvalidArgumentException();
        }

        $currentIncomes = $this->incomes;
        unset($currentIncomes[$member->id]);

        return new self(
            $this->members,
            $this->bills,
            $this->expenses,
            $currentIncomes
        );
    }

    public function getTotalIncome(): Amount
    {
        return array_reduce(
            $this->incomes,
            function (Amount $carry, Amount $income) {
                return $carry->add($income);
            },
            new Amount(0)
        );
    }

    /**
     * @throws Exception
     */
    public function getRatiosFromIncome(): array
    {
        if (count($this->incomes) !== count($this->members)) {
            throw new Exception("You need to set incomes for every member.");
        }

        $totalIncome = $this->getTotalIncome();

        return array_combine(
            array_map(
                function (Member $member) {
                    return $member->id;
                },
                $this->members->all()
            ),
            array_map(
                function (Member $member) use ($totalIncome) {
                    return $this->incomes[$member->id]->toCents() / $totalIncome->toCents();
                },
                $this->members->all()
            )
        );
    }

    public function toMovements(): Collection
    {
        if (count($this->incomes) !== count($this->members)) {
            throw new Exception("You need to set incomes for every member.");
        }

        $member = $this->members->first();
        $household = $member->household;

        return MovementsServiceCalculator::compute(
            $this->members,
            $this->bills,
            $this->expenses,
            $this->getRatiosFromIncome(),
            $household->jointAccount()
        );
    }

    public static function create(): MovementsService
    {
        return new self();
    }
}
