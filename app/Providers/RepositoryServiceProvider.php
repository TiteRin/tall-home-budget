<?php

namespace App\Providers;

use App\Repositories\BillRepository;
use App\Repositories\EloquentBillRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(BillRepository::class, EloquentBillRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
