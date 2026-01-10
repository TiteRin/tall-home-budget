<?php

namespace Tests\Unit\Models;

use App\Models\ExpenseTab;
use Illuminate\Support\Carbon;

it('calculates the correct monthly period', function () {

    $tab = ExpenseTab::factory()->make([
        'period_start_day' => 5,
        'period_end_day' => 5
    ]);

    $period = $tab->periodFor(Carbon::create(2026, 01, 10));

    expect($period->start->toDateString())->toEqual('2026-01-05')
        ->and($period->end->toDateString())->toEqual('2026-02-04');
});
