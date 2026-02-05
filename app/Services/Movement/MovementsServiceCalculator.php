<?php

namespace App\Services\Movement;

use App\Domains\Entities\JointAccount;
use App\Domains\ValueObjects\Amount;
use App\Domains\ValueObjects\Balance;
use App\Domains\ValueObjects\ChargesCollection;
use App\Enums\DistributionMethod;
use App\Models\Member;
use Illuminate\Support\Collection;

class MovementsServiceCalculator
{

    private function __construct(
        protected Collection        $members,
        protected ChargesCollection $charges,
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
        ChargesCollection $charges,
        array             $ratios,
        ?JointAccount     $jointAccount
    ): Collection
    {
        $calculator = new self($members, $charges, $ratios, $jointAccount);
        return $calculator->toMovements();
    }

    private function getBalanceForJointAccount(): Balance
    {
        return new Balance(
            $this->jointAccount,
            $this->charges->getTotalAmountForJointAccount()
        );
    }

    private function getBalanceForMember(Member $member): Balance
    {
        $nbMembers = count($this->members);
        $ratio = $this->ratios[$member->id];

        $chargesPaid = $this->charges->getTotalAmountForMember($member);

        $owedEqual = new Amount($this->getTotalEqual()->toCents() / $nbMembers);
        $owedProrata = new Amount($this->getTotalProrata()->toCents() * $ratio);
        $totalOwed = $owedEqual->add($owedProrata);

        return new Balance(
            $member,
            $chargesPaid->subtract($totalOwed)
        );
    }

    private function getTotalEqual(): Amount
    {
        return $this->charges->getTotalAmountForDistributionMethod(DistributionMethod::EQUAL);
    }

    private function getTotalProrata(): Amount
    {
        return $this->charges->getTotalAmountForDistributionMethod(DistributionMethod::PRORATA);
    }
}
