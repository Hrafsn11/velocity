<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

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
        if (app()->environment('production')) {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // Register Observers
        \App\Models\KanbanTask::observe(\App\Observers\KanbanTaskObserver::class);

        // Update config values dynamically from helper
        try {
            Config::set('app.name', app_config('app_name'));
        } catch (\Exception $e) {
            // If table doesn't exist yet (during migration), skip
        }
    }
}
