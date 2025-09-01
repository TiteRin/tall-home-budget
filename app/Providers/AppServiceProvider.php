<?php

namespace App\Providers;

use App\Repositories\Contracts\BillRepository;
use App\Repositories\Eloquent\EloquentBillRepository;
use App\Services\Household\HouseholdService;
use App\Services\Household\HouseholdServiceContract;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(HouseholdServiceContract::class, HouseholdService::class);
        $this->app->bind(BillRepository::class, EloquentBillRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
