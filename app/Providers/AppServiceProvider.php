<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\Http\ViewComposers\SidebarMenuComposer;

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
        Paginator::useBootstrap();
        Paginator::defaultView('vendor.pagination.bootstrap-5');
        
        // Register sidebar menu composer for housing theme and admin panel
        // View::composer('housingTheme.partials.dashboard-sidebar', SidebarMenuComposer::class);
        // View::composer('admin.partials.admin-sidebar', SidebarMenuComposer::class);
    }
}
