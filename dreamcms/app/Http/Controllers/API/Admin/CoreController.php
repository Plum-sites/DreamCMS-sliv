<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonateGroup;
use App\Models\Server;
use App\Models\User;
use Illuminate\Http\Request;

class CoreController extends Controller{

    public function loadInfo(Request $request){
        /* @var User $user */
        $user = \Auth::user();

        if ($user->hasPermissionTo("admin.access.access")){
            return \Response::json([
                'user' => $user,
                'role' => $user->getSiteRole(),
                'roles' => $user->getAllRoles()->pluck('name'),
                'permissions'=> $user->getAllPermissions()->pluck('name'),
                'servers' => $user ? Server::all('id', 'name', 'version', 'active') : [],
                'dgroups' => $user ? DonateGroup::all('id', 'name', 'pexname', 'active') : []
            ]);
        }else{
            abort(403);
        }
    }

}