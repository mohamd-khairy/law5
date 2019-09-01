<?php

namespace App\Providers;

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
        /*if ($this->app->environment() == 'local') {
            $this->app->register(\Wn\Generators\CommandsServiceProvider::class);
        }*/
        if ($this->app->environment() == 'local') {
        $this->app->register('Wn\Generators\CommandsServiceProvider');
        }

        $this->app->singleton(\Illuminate\Contracts\Routing\ResponseFactory::class, function ($app) {
            return new \Illuminate\Routing\ResponseFactory(
                $app[\Illuminate\Contracts\View\Factory::class],
                $app[\Illuminate\Routing\Redirector::class]
            );
        });
    }
}
