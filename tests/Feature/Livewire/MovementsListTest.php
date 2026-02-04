<?php

use App\Enums\DistributionMethod;
use App\Models\Expense;
use App\Models\ExpenseTab;
use App\Models\Household;
use App\Models\Member;
use App\Models\User;

describe('MovementsList Component', function () {
    beforeEach(function () {
        $this->household = Household::factory()->create();
        $this->member = Member::factory()->create(['household_id' => $this->household->id]);
        $this->user = User::factory()->create(['member_id' => $this->member->id]);
        $this->actingAs($this->user);

        $this->expenseTab = ExpenseTab::factory()->create(['household_id' => $this->household->id]);
        Expense::factory()->count(20)->create([
            'expense_tab_id' => $this->expenseTab->id,
            'member_id' => $this->member->id,
            'amount' => 1000,
            'distribution_method' => DistributionMethod::EQUAL,
            'spent_on' => now()
        ]);
    });
});
