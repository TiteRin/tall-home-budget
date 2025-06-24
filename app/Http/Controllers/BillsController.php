<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bill;

class BillsController extends Controller
{
    public function index()
    {
        $bills = Bill::all();

        return view('bills.index', compact('bills'));
    }
}
