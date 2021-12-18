<?php

namespace App\Providers;

use Backpack\Settings\app\Models\Setting as Setting;
use Config;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Route;

class SettingsServiceProvider extends \Backpack\Settings\SettingsServiceProvider{

    public function boot()
    {
        $this->setupRoutes($this->app->router);

        if (!$this->is_run_in_console()){
            if (\Cache::has('laravel_settings')){
                $settings = \Cache::get('laravel_settings');
            }else {
                $settings = Setting::all();
                \Cache::set('laravel_settings', $settings, 60);
            }

            $rewrite = [
                'smtp_host' => 'mail.host',
                'smtp_port' => 'mail.port',
                'smtp_user' => 'mail.username',
                'smtp_pass' => 'mail.password',

                'smtp_from' => 'mail.from.address',

                'captcha_pubkey' => 'recaptcha.public_key',
                'captcha_privkey' => 'recaptcha.private_key',
            ];

            foreach ($settings as $key => $setting) {
                Config::set('settings.' . $setting->key, $setting->value);

                if(in_array($setting->key, array_keys($rewrite))){
                    Config::set($rewrite[$setting->key], $setting->value);
                }
            }
        }

        $this->publishes([__DIR__.'/database/migrations/' => database_path('migrations')], 'migrations');

        $this->publishes([__DIR__.'/resources/lang' => resource_path('lang/vendor/backpack')], 'lang');
    }

    function is_run_in_console(): bool
    {
        if (true === (bool) getenv('RR_HTTP')) {
            return false;
        }
        return \App::runningInConsole();
    }
}
