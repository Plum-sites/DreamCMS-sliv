<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ForumMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = \Auth::user();

        if ($user){
            $online = Cache::get('forum_online', []);

            $user->ftime = time();
            $online[$user->id] = $user;

            foreach ($online as $id => $user2){
                if ((time() - $user2->ftime) > 600) unset($online[$user2->id]);
            }
            Cache::forever('forum_online', $online);
        }

        return $next($request);
    }

}