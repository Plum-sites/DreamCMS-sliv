<?php

namespace App\Http\Middleware;
use Closure;

class ThrottleRequests extends \Illuminate\Routing\Middleware\ThrottleRequests
{
    public function handle($request, Closure $next, $maxAttempts = 60, $decayMinutes = 1, $prefix = '')
    {
        if (\Auth::user() && \Auth::user()->isModer()){
            return $next($request);
        }

        return parent::handle($request, $next, $maxAttempts, $decayMinutes, $prefix);
    }
}