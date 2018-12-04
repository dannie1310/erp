<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ContextServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            \App\Contracts\Context::class,
            \App\Repositories\ContextSession::class
        );
        $this->app->singleton('app.context', \App\Repositories\ContextSession::class);
    }
}
