<?php

namespace App\Providers;

use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Schema;
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
    // public function boot(\Illuminate\Http\Request $request)
    // {
    //    // Schema::defaultStringLength(191);

    // }
    public function boot(UrlGenerator $url)
    {
        if (env('APP_ENV') == 'production') {
            $url->forceScheme('https');
        }
    }
    // {
    //     if (!empty( env('NGROK_URL') ) && $request->server->has('HTTP_X_ORIGINAL_HOST')) {
    //         $this->app['url']->forceRootUrl(env('NGROK_URL'));
    //     }
    // }





}
