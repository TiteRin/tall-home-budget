<?php

namespace App\Http\Controllers;

use App\Services\BillService;
use Illuminate\Contracts\View\View;

class BillsController extends Controller
{
    public function __construct(
        private BillService $billService
    ) {}

    public function index(): View
    {
        $data = $this->billService->getBillsForHousehold();

        return view('bills.index', [
            'bills' => $data['bills']->getData(),
            'total_amount' => $data['bills']->getMeta()['total_amount'] ?? 0,
            'total_amount_formatted' => $data['bills']->getMeta()['total_amount_formatted'] ?? '0,00 €',
        ]);
    }

    public function settings(): View {

        return view('bills.settings');
    }
}
