<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Routing\UrlGenerator;

class AppServiceProvider extends ServiceProvider
{

    public function boot(UrlGenerator $url)
    {
        //
        if (env('REDIRECT_HTTPS')) {
            $url->forceSchema('https');
        }
    }
}
