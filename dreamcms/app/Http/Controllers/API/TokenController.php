<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JWTAuth;

class TokenController extends Controller
{

    /**
     * Create and return a token if the user is logged in
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function token(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'not_logged_in'], 401);
        }

        $user = Auth::user();
        $claims = [
            'id' => $user->id,
            'login'=> $user->login,
            'uuid' => $user->uuid,
            'reputation' => $user->reputation,
            'role' => $user->getSiteRole(),
            'moder' => $user->isModer(),
            'reg_time' => $user->reg_time
        ];

        $token = JWTAuth::fromUser($user, $claims);
        return response()->json(['token' => $token]);
    }

}
