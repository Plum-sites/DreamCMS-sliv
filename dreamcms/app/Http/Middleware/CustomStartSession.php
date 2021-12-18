<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\StartSession;

class CustomStartSession extends StartSession {
    
    public function handle($request, Closure $next)
    {
        return parent::handle($request, $next);
    }
    
    public function storeCurrentUrl(Request $request, $session){
        if(!$request->is('/skin/*', '/head/*')){
            parent::storeCurrentUrl($request, $session);
        }
    }
    
}