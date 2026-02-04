<?php

use App\Domains\Converters\BillToChargeConverter;
use App\Domains\Converters\ChargesAssembler;
use App\Domains\Converters\ExpenseToChargeConverter;
use App\Domains\ValueObjects\Amount;
use App\Enums\DistributionMethod;
use App\Models\Bill;
use App\Models\Expense;
use App\Services\Bill\BillsCollection;
use App\Services\Expense\ExpensesCollection;

describe('ChargesAssembler', function () {
    beforeEach(function () {
        $this->billConverter = new BillToChargeConverter();
        $this->expenseConverter = new ExpenseToChargeConverter();
        $this->assembler = new ChargesAssembler($this->billConverter, $this->expenseConverter);
    });

    test('it assembles charges from bills', function () {
        // Given
        $bill = new Bill();
        $bill->id = 1;
        $bill->amount = new Amount(1000);
        $bill->distribution_method = DistributionMethod::EQUAL;
        $bills = new BillsCollection([$bill]);

        // When
        $result = $this->assembler->fromBills($bills)->assemble();

        // Then
        expect($result)->toHaveCount(1)
            ->and($result->first()->getAmountOrZero()->toCents())->toBe(1000);
    });

    test('it assembles charges from expenses', function () {
        // Given
        $expense = new Expense();
        $expense->id = 1;
        $expense->amount = new Amount(2000);
        $expense->distribution_method = DistributionMethod::PRORATA;
        $expenses = new ExpensesCollection([$expense]);

        // When
        $result = $this->assembler->fromExpenses($expenses)->assemble();

        // Then
        expect($result)->toHaveCount(1)
            ->and($result->first()->getAmountOrZero()->toCents())->toBe(2000);
    });

    test('it can reset charges', function () {
        // Given
        $bill = new Bill();
        $bill->id = 1;
        $bill->amount = new Amount(1000);
        $bill->distribution_method = DistributionMethod::EQUAL;
        $bills = new BillsCollection([$bill]);

        $this->assembler->fromBills($bills);

        // When
        $this->assembler->reset();
        $result = $this->assembler->assemble();

        // Then
        expect($result)->toHaveCount(0);
    });

    test('it can chain methods', function () {
        // Given
        $bill = new Bill();
        $bill->id = 1;
        $bill->amount = new Amount(1000);
        $bill->distribution_method = DistributionMethod::EQUAL;
        $bills = new BillsCollection([$bill]);

        $expense = new Expense();
        $expense->id = 1;
        $expense->amount = new Amount(2000);
        $expense->distribution_method = DistributionMethod::PRORATA;
        $expenses = new ExpensesCollection([$expense]);

        // When
        $result = $this->assembler
            ->fromBills($bills)
            ->fromExpenses($expenses)
            ->assemble();

        // Then
        expect($result)->toHaveCount(2);
    });
});
