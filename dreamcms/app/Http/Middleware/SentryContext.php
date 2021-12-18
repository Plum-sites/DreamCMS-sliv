<?php

namespace App\Http\Middleware;

use Closure;
use Sentry\State\Scope;

class SentryContext {
    /**
    * Handle an incoming request.
    *
    * @param \Illuminate\Http\Request $request
    * @param \Closure                 $next
    *
    * @return mixed
    */
    public function handle($request, Closure $next)
    {
        if (auth()->check() && app()->bound('sentry')) {
            \Sentry\configureScope(function (Scope $scope): void {
                $user = auth()->user();
                $scope->setUser([
                    'id' => $user->id,
                    'login' => $user->login,
                    'email' => $user->email,
                ]);
            });
        }

        return $next($request);
    }
}