<?php

namespace App\Http\Controllers;

use App\Services\Household\HouseholdServiceContract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(HouseholdServiceContract $householdService): RedirectResponse|View
    {
        if (Auth::guest()) {
            return view('welcome');
        }

        $household = $householdService->getCurrentHousehold();

        if (!$household) {
            return redirect()->route('household.settings');
        }

        return view('home', compact('household'));
    }
}
