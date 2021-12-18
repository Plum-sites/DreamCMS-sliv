<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Contracts\Auth\Access\Authorizable;

use Spatie\Permission\Exceptions\PermissionDoesNotExist;


class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        //'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //Passport::routes();

        $this->app[\Illuminate\Contracts\Auth\Access\Gate::class]->before(function (Authorizable $user, string $ability) {
            try {
                if (method_exists($user, 'hasPermissionTo')) {
                    return $user->hasPermissionTo($ability) ?: null;
                }
            } catch (PermissionDoesNotExist $e) {
            }
        });

        Gate::define('crud', 'App\Policies\CrudPolicy@handle');
        Gate::define('ext', 'App\Policies\ExtendedPolicy@handle');
        Gate::define('forum', 'App\Policies\ForumPolicy@handle');
    }
}
