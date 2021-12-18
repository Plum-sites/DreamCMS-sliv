<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class ViewMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = \Auth::user();

        View::share('logged', $user != null);

        if ($user){
            View::share('user', $user);
            View::share('head', '/head/' . $user->uuid);
        }

        View::share('request', $request);
        View::share('url', $request->url());

        if (config('settings.techworks', true)){
            if($request->ip() !== '5.39.74.3' && substr_count($request->url(), 'payment') <= 0){
                //return view('offline');
            }
        }

        return $next($request);
    }

}
