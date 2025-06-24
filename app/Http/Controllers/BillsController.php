<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bill;
use App\Models\Household;

class BillsController extends Controller
{
    public function index()
    {
        $household = Household::orderBy('created_at')->first();
        $bills = $household->bills ?? collect([]);

        return view('bills.index', compact('bills'));
    }
}
