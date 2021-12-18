<?php

namespace App\Http\Middleware;

use Closure;

class SpecialOffer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (\Auth::check())
            \App\Models\SpecialOffer::processPlayer(\Auth::user());

        return $next($request);
    }
}
