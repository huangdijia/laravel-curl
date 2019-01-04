<?php

namespace Huangdijia\Curl;

use Illuminate\Support\ServiceProvider;

class CurlServiceProvider extends ServiceProvider
{
    protected $defer = true;

    public function boot()
    {
        //
    }

    public function register()
    {
        $this->app->singleton(Curl::class, function () {
            return new Curl();
        });

        $this->app->alias(Curl::class, 'curl');

    }

    public function provides()
    {
        return [
            Curl::class,
            'curl',
        ];
    }
}