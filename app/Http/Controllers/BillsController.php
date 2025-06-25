<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BillService;
use App\ViewModels\BillsIndexViewModel;

class BillsController extends Controller
{
    public function __construct(
        private BillService $billService
    ) {}

    public function index()
    {
        $data = $this->billService->getBillsForHousehold();
        
        return view('bills.index', [
            'bills' => $data['bills']->getData(),
            'total_amount' => $data['bills']->getMeta()['total_amount'] ?? 0,
            'total_amount_formatted' => $data['bills']->getMeta()['total_amount_formatted'] ?? '0,00 €',
        ]);
    }
}
