<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        // Laravel only scans the top level of database/migrations, so module folders are registered here.
        $this->loadMigrationsFrom(database_path('migrations/duolingo'));
    }
}
