<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Smtp;
use App\Http\Helper;

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
        try {

            // Set SMTP Detail
            $smtp = cache()->rememberForever(Smtp::CACHE_KEYS['main'], function() {
                return Smtp::latest()->first();
            });

            if ($smtp) {
                $smtp = Smtp::latest()->first();
                config()->set('mail', array_merge(config('mail'), [
                    'driver' => 'smtp',
                    'host' => $smtp->host,
                    'port' => $smtp->port,
                    'encryption' => $smtp->encryption,
                    'username' => $smtp->username,
                    'password' => $smtp->password,
                    'from' => [
                        'address' => $smtp->from_email,
                        'name' => $smtp->from_name
                    ]
                ]));
            }

        } catch (\Exception $e) {
            // Report any exceptions that occur during the configuration setup
            //report($e);
        }

        $siteSetting = Helper::siteSetting();
        if($siteSetting) {
            view()->share('logo', $siteSetting->logo);
        } else {
            view()->share('logo',url('logo/logo2.png'));
        }
    }
}