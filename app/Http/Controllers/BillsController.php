<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bill;

class BillsController extends Controller
{
    public function index()
    {
        $bills = Bill::all();

        $view = "Les dépenses du foyer";

        if ($bills->isEmpty()) {
            $view .= "Aucune dépense";
        }

        return $view;
    }
}
