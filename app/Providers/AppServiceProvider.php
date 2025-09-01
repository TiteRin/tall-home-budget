<?php

namespace App\Providers;

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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
