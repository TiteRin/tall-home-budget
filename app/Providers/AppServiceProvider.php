<?php

namespace App\Providers;

use App\Services\Household\CurrentHouseholdService;
use App\Services\Household\CurrentHouseholdServiceContract;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CurrentHouseholdServiceContract::class, CurrentHouseholdService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
