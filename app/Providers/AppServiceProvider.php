<?php

namespace App\Providers;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
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

        if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
            $mail = DB::table('settings')->first();
            if ($mail) //checking if table is not empty
            {
                $config = array(
                    'driver'     => 'smtp',
                    'host'       => $mail->mailServer,
                    'port'       => $mail->mailServerPort,
                    'from'       => array('address' => $mail->fromEmail, 'name' => "Law5 Team"),
                    'encryption' => $mail->mailEnableSSL,
                    'username'   => $mail->fromEmail,
                    'password'   => Crypt::decrypt($mail->fromEmailPassword),
                    'sendmail'   => '/usr/sbin/sendmail -bs',
                    'pretend'    => false,
                );
                Config::set('mail', $config);
                // dd(Config::get('mail'));
            }
        }
    }
}
