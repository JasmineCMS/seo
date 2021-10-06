<?php

namespace Jasmine\Seo;

use Illuminate\Support\ServiceProvider;

class JasmineSeoServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('jasmine-seo', fn() => new JasmineSeo());
    }

    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
