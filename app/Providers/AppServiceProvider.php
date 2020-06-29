<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(
            \A17\Twill\Http\Controllers\Admin\SettingController::class,
            \App\Http\Controllers\Admin\SettingController::class,
        );

        $this->app->bind(
            \A17\Twill\Http\Controllers\Admin\DashboardController::class,
            \App\Http\Controllers\Admin\DashboardController::class,
        );

        if (\App::environment('production')) {
            URL::forceScheme('https');
        }
    }
}
