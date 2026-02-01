<?php

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\LogoutController;
use App\Livewire\Admin\AdminDashboard;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Passwords\Confirm;
use App\Livewire\Auth\Passwords\Email;
use App\Livewire\Auth\Passwords\Reset;
use App\Livewire\Auth\Register;
use App\Livewire\Auth\Verify;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::controller(App\Http\Controllers\BillsController::class)->group(function() {
    Route::get('/bills', 'index')->name('bills');
    Route::get('/bills/settings', 'settings')->name('bills.settings');
    Route::post('/bills', 'store')->name('bills.store');
});

Route::controller(App\Http\Controllers\ExpenseTabsController::class)->group(function () {
    Route::get('/expense-tabs', 'index')->name('expense-tabs.index');
});

Route::get('/household/settings', function() {
    return view('household');
})->name('household.settings');

Route::middleware('guest')->group(function () {
    Route::get('login', Login::class)
        ->name('login');

    Route::get('register', Register::class)
        ->name('register');
});

Route::get('password/reset', Email::class)
    ->name('password.request');

Route::get('password/reset/{token}', Reset::class)
    ->name('password.reset');

Route::middleware('auth')->group(function () {
    Route::get('email/verify', Verify::class)
        ->middleware('throttle:6,1')
        ->name('verification.notice');

    Route::get('password/confirm', Confirm::class)
        ->name('password.confirm');
});

Route::middleware('auth')->group(function () {
    Route::get('email/verify/{id}/{hash}', EmailVerificationController::class)
        ->middleware('signed')
        ->name('verification.verify');

    Route::post('logout', LogoutController::class)
        ->name('logout');

    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');
});

Route::get('/mentions-legales', function () {
    return view('legal.mentions-legales');
})->name('mentions-legales');

Route::get('/cgu', function () {
    return view('legal.cgu');
})->name('cgu');

Route::get('/confidentialite', function () {
    return view('legal.confidentialite');
})->name('confidentialite');

Route::middleware('admin.auth')->group(function () {
    Route::get('/admin', AdminDashboard::class)->name('admin.dashboard');
});
