<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        view()->composer('*', function ($view) {
            $view->with('siteSettings', \App\Models\SiteSetting::find(1));
            $view->with('frontServices', Schema::hasTable('services') ? \App\Models\Service::active()->orderBy('sort_order')->get() : collect());
        });
    }
}
