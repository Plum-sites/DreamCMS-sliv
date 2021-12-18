<?php

namespace App\Providers;

use Carbon\Carbon;
use Tymon\JWTAuth\Exceptions\UserNotDefinedException;
use Tymon\JWTAuth\Providers\LaravelServiceProvider;

class JWTAuthServiceProvider extends LaravelServiceProvider
{
    /**
     * Extend Laravel's Auth.
     *
     * @return void
     */
    protected function extendAuthGuard()
    {
        $this->app['auth']->extend('jwt', function ($app, $name, array $config) {
            $guard = new JWTGuard(
                $app['tymon.jwt'],
                $app['auth']->createUserProvider($config['provider']),
                $app['request']
            );

            $app->refresh('request', $guard, 'setRequest');

            return $guard;
        });
    }
}

class JWTGuard extends \Tymon\JWTAuth\JWTGuard {
    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if ($this->user !== null) {
            return $this->user;
        }

        if ($this->jwt->setRequest($this->request)->getToken() &&
            ($payload = $this->jwt->check(true)) &&
            $this->validateSubject()
        ) {
            $user = $this->provider->retrieveById($payload['sub']);

            if ($this->tokenInvalidated($user, $payload['iat'])) return null;

            return $user;
        }

        return null;
    }

    public function tokenInvalidated($user, $tokenTime)
    {
        return $user && $user->token_reset && $user->token_reset > $tokenTime;
    }
}