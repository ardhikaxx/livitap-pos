<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\User;
use App\Models\Business;
use App\Observers\UserObserver;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        User::observe(UserObserver::class);

        // Share active business to all views
        View::composer('*', function ($view) {
            if (auth()->check() && session('business_id')) {
                $business = Business::find(session('business_id'));
                $view->with('activeBusiness', $business);
            } else {
                $view->with('activeBusiness', null);
            }
        });
    }
}

