<?php

namespace App\Providers;

use App\Http\Receivers\Wp;
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
        $receiver = app('receiver');

        $receiver->extend('wp', function ($app) {
            return new Wp();
        });
    }
}
