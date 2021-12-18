<?php

namespace App\Http\Middleware;

use Closure;

class Elfinder
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
        if(!\Auth::user()->hasPermissionTo('elfinder.access.access')){
            if ($request->ajax() || $request->wantsJson()) {
                return response('У вас нет доступа к редактору файлов!', 403);
            } else {
                abort(403, 'У вас нет доступа к редактору файлов!');
            }
        }

        return $next($request);
    }
}
