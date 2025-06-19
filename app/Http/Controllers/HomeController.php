<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Household;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): RedirectResponse|View
    {
        $household = Household::orderBy('id')->first();
        
        if (!$household) {
            return redirect()->route('household.settings');
        }
        
        return view('home', compact('household'));
    }
}
