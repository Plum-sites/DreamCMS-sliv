<?php

namespace App\Providers;

use Carbon\Carbon;
use DB;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Wkhooy\ObsceneCensorRus;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //Paginator::useBootstrapThree();
//        DB::listen(function ($query) {
//            var_dump([
//                $query->sql,
//                $query->bindings,
//                $query->time
//            ]);
//        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        \URL::forceScheme('https');
        ObsceneCensorRus::$exceptions[] = 'ibath';
        ObsceneCensorRus::$exceptions[] = 'IBath';

        setlocale(LC_ALL, 'ru_RU.utf8', 'ru_RU', 'ru');
        Carbon::setLocale('ru');
    }
}
