<?php

namespace App\Services\Movement;

use App\Domains\Entities\JointAccount;
use App\Domains\ValueObjects\Amount;
use App\Domains\ValueObjects\Balance;
use App\Enums\DistributionMethod;
use App\Models\Member;
use App\Services\Bill\BillsCollection;
use App\Services\Expense\ExpenseCollection;
use Illuminate\Support\Collection;

class MovementsServiceCalculator
{

    private function __construct(
        protected Collection        $members,
        protected BillsCollection   $bills,
        protected ExpenseCollection $expenses,
        protected array             $ratios,
        protected ?JointAccount     $jointAccount
    )
    {
        // Vérifier que bills et members sont synchronisés
        ;
    }

    private function computeBalances(): BalancesCollection
    {
        $balances = new BalancesCollection();

        foreach ($this->members as $member) {
            $balances->push($this->getBalanceForMember($member));
        }

        if ($this->jointAccount) {
            $balances->push($this->getBalanceForJointAccount());
        }

        return $balances;
    }

    public function toMovements(): Collection
    {
        $movements = collect();
        $balances = $this->computeBalances();
        $creditors = $balances->getCreditors();
        $debtors = $balances->getDebtors();

        $creditor = $creditors->shift();
        $debtor = $debtors->shift();

        while ($creditor !== null && $debtor !== null) {

            $amount = new Amount(min(
                $creditor->abs()->toCents(),
                $debtor->abs()->toCents()
            ));

            $movements->push(
                new Movement(
                    memberFrom: $debtor->member,
                    memberTo: $creditor->member,
                    amount: $amount
                )
            );

            $creditor->amount = $creditor->amount->subtract($amount);
            $debtor->amount = $debtor->amount->add($amount);

            if ($creditor->amount->toCents() === 0) {
                $creditor = $creditors->shift();
            }

            if ($debtor->amount->toCents() === 0) {
                $debtor = $debtors->shift();
            }
        }

        return $movements;
    }

    public static function compute(
        Collection        $members,
        BillsCollection   $bills,
        ExpenseCollection $expenses,
        array             $ratios,
        ?JointAccount     $jointAccount
    ): Collection
    {
        $calculator = new self($members, $bills, $expenses, $ratios, $jointAccount);
        return $calculator->toMovements();
    }

    private function getBalanceForJointAccount(): Balance
    {
        $totalBillsJoint = $this->bills->getTotalForJointAccount();
        $totalExpensesJoint = $this->expenses->getTotalForJointAccount();

        return new Balance(
            $this->jointAccount,
            $totalBillsJoint->add($totalExpensesJoint)
        );
    }

    private function getBalanceForMember(Member $member): Balance
    {
        $nbMembers = count($this->members);
        $ratio = $this->ratios[$member->id];

        $billsPaid = $this->bills->getTotalForMember($member);
        $expensesPaid = $this->expenses->getTotalForMember($member);
        $totalPaid = $billsPaid->add($expensesPaid);

        $owedEqual = new Amount($this->getTotalEqual()->toCents() / $nbMembers);
        $owedProrata = new Amount($this->getTotalProrata()->toCents() * $ratio);
        $totalOwed = $owedEqual->add($owedProrata);

        return new Balance(
            $member,
            $totalPaid->subtract($totalOwed)
        );
    }

    private function getTotalEqual(): Amount
    {
        $totalBillsEqual = $this->bills->getTotalForDistributionMethod(DistributionMethod::EQUAL);
        $totalExpensesEqual = $this->expenses->getTotalForDistributionMethod(DistributionMethod::EQUAL);

        return $totalBillsEqual->add($totalExpensesEqual);
    }

    private function getTotalProrata(): Amount
    {
        $totalBillsProrata = $this->bills->getTotalForDistributionMethod(DistributionMethod::PRORATA);
        $totalExpenseProrata = $this->expenses->getTotalForDistributionMethod(DistributionMethod::PRORATA);

        return $totalBillsProrata->add($totalExpenseProrata);
    }
}
