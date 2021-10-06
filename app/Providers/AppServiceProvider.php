<?php

namespace App\Providers;

use App\Foundation\Runners\Factory;
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
        /**
         * Register the runner factory.
         */
        $this->app->singleton(Factory::class, function($app) {
            return new Factory($app);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
