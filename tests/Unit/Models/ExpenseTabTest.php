<?php

namespace Tests\Unit\Models;

use App\Models\ExpenseTab;
use Illuminate\Support\Carbon;

it('calculates the correct monthly period', function () {

    $tab = ExpenseTab::factory()->make([
        'period_start_day' => 5,
        'period_end_day' => 5
    ]);

    $periodJanuary = $tab->periodFor(Carbon::create(2026, 01, 10));
    $periodDecember = $tab->periodFor(Carbon::create(2026, 01, 03));

    expect($periodJanuary->start->toDateString())->toEqual('2026-01-05')
        ->and($periodJanuary->end->toDateString())->toEqual('2026-02-04')
        ->and($periodDecember->start->toDateString())->toEqual('2025-12-05')
        ->and($periodDecember->end->toDateString())->toEqual('2026-01-04');
});
