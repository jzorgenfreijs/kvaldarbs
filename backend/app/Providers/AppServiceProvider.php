<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Test;
use App\Observers\TestObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Test::observe(TestObserver::class);
    }
}
