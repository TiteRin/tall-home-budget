<?php

namespace App\Services\Movement;

use App\Domains\Converters\ChargesAssembler;
use App\Domains\ValueObjects\Amount;
use App\Domains\ValueObjects\ChargesCollection;
use App\Models\Expense;
use App\Models\Member;
use App\Services\Bill\BillsCollection;
use App\Services\Expense\ExpensesCollection;
use Exception;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class MovementsService
{
    private function __construct(
        protected ?Collection        $members = new Collection(),
        protected ?ChargesCollection $charges = new ChargesCollection(),
        protected ?array             $incomes = []
    )
    {
        ;
    }

    public function withMembers(Collection $members)
    {
        return new self(
            $members,
            $this->charges,
            $this->incomes
        );
    }

    public function withBills(BillsCollection $bills)
    {
        $chargeAssembler = ChargesAssembler::create();

        $charges = $this->charges;
        $charges = $charges->merge(
            $chargeAssembler->fromBills($bills)->assemble()
        );

        return new self(
            $this->members,
            $charges,
            $this->incomes
        );
    }

    public function withExpenses(ExpensesCollection $expenses)
    {
        $chargeAssembler = ChargesAssembler::create();

        $charges = $this->charges;
        $charges = $charges->merge(
            $chargeAssembler->fromExpenses($expenses)->assemble()
        );

        return new self(
            $this->members,
            $charges,
            $this->incomes
        );
    }

    public function withCharges(ChargesCollection $charges)
    {
        return new self(
            $this->members,
            $charges,
            $this->incomes
        );
    }

    public function addExpenses(ExpensesCollection $expenses)
    {
        return new self(
            $this->members,
            $this->charges,
            $this->incomes
        );
    }

    public function addExpense(Expense $expense)
    {
        return new self(
            $this->members,
            $this->charges,
            $this->incomes
        );
    }

    public function hasMembers(): bool
    {
        return count($this->members) > 0;
    }

    public function hasBills(): bool
    {
        return $this->hasCharges();
    }

    public function hasExpenses(): bool
    {
        return $this->hasCharges();
    }

    public function hasCharges(): bool
    {
        return count($this->charges) > 0;
    }

    public function withIncomeFor(Member $member, Amount $amount): MovementsService
    {
        if (!$this->members->contains('id', $member->id)) {
            throw new InvalidArgumentException();
        }

        $currentIncomes = $this->incomes;
        $currentIncomes[$member->id] = $amount;

        return new self(
            $this->members,
            $this->charges,
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
            $this->charges,
            $currentIncomes
        );
    }

    public function withIncomes(array $incomes): MovementsService
    {

        foreach ($incomes as $member_id => $income) {

            if ($income === null) {
                unset($incomes[$member_id]);
                continue;
            }

            if (!array_any($this->members->all(), function (Member $m) use ($member_id) {
                return $m->id === $member_id;
            })) {
                throw new InvalidArgumentException("The Member [$member_id] is not a part of the service.");
            }
        }

        return new self(
            $this->members,
            $this->charges,
            $incomes
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
            return collect();
        }

        $member = $this->members->first();
        $household = $member->household;

        return MovementsServiceCalculator::compute(
            $this->members,
            $this->charges,
            $this->getRatiosFromIncome(),
            $household->jointAccount()
        );
    }

    // Income => (Member, Amount) ??

    public static function create(): MovementsService
    {
        return new self();
    }
}
