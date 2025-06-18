<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Household;

class HomeController extends Controller
{
    public function index()
    {
        $household = Household::orderBy('id')->first();
        
        return view('home', compact('household'));
    }
}
