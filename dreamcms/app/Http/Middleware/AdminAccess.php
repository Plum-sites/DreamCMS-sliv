<?php

namespace App\Http\Middleware;

use Closure;

class AdminAccess
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
        if (\Auth::user() && \Auth::user()->isSuperAdmin()){
            return $next($request);
        }
        
        if(!\Auth::user() || !\Auth::user()->hasPermissionTo('admin.access.access')){
            if ($request->ajax() || $request->wantsJson()) {
                return response('Недостаточно прав! [access]', 403);
            } else {
                return abort(403, 'Недостаточно прав! [access]');
            }
        }
        
        //Костыль для настроек из пакета
        /*if ($request->is('admin/setting/*')){
            if (true || !\Auth::user() || !\Auth::user()->isSuperAdmin()){
                return abort(403, 'Только для супер-администраторов!');
            }
        }*/

        return $next($request);
    }
}
