<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Response;

class PermissionsController extends Controller{

    public function __construct()
    {
        $this->middleware(function ($request, $next)
        {
            abort_if(!\Auth::user()->isSuperAdmin(),403, 'Только для супер-администраторов!');
            
            return $next($request);
        });
    }
    
    public function index(Request $request){
        $servers = Server::getActive();
        return view('admin.permissions.index', ['servers' => $servers]);
    }

    public function server(Request $request, $server){
        /* @var Server $server */
        $server = Server::findOrFail($server);
        $groups = $server->getPermissionManager()->listGroup();

        return view('admin.permissions.server', ['server' => $server, 'groups' => $groups]);
    }

    private function parseUser($info){
        if($user = User::fromUUID($info)) return $user;
        if($user = User::where('login', $info)->get()->first()) return $user;
        return false;
    }


    public function group(Request $request){
        $group = $request->get('group');

        /* @var Server $server */
        $server = Server::findOrFail($request->get('server'));

        return Response::json([
            'name' => $group,
            'prefix' => $server->getPermissionManager()->getGroupPrefix($group),
            'suffix' => $server->getPermissionManager()->getGroupSuffix($group)
        ]);
    }


    public function user(Request $request){
        $user = $this->parseUser($request->get('user'));

        /* @var Server $server */
        $server = Server::findOrFail($request->get('server'));

        return Response::json([
            'login' => $user->login,
            'uuid' => $user->uuid,
            'prefix' => $server->getPermissionManager()->getUserPrefix($user->uuid),
            'suffix' => $server->getPermissionManager()->getUserSuffix($user->uuid),
            'permissions' => $server->getPermissionManager()->getUserPermissions($user->uuid)
        ]);
    }

    public function group_players(Request $request){
        $group = $request->get('group');

        /* @var Server $server */
        $server = Server::findOrFail($request->get('server'));

        /* @var Collection $uuids */
        $group_players = $server->getPermissionManager()->playersInGroup($group);

        $group_players->map(function ($item, $key) use (&$return){
            if($user = User::fromUUID($item->uuid)) $item->login = $user->login;
            return $item;
        });

        return Response::json($group_players);
    }

    public function user_groups(Request $request){
        $user = $request->get('user');

        $user = $this->parseUser($user)->uuid;

        /* @var Server $server */
        $server = Server::findOrFail($request->get('server'));

        /* @var Collection $uuids */
        $group_players = $server->getPermissionManager()->playerGroups($user);

        $group_players->map(function ($item, $key) use (&$return){
            if($user = User::fromUUID($item->uuid)) $item->login = $user->login;
            return $item;
        });

        return Response::json($group_players);
    }

    public function user_removegroup(Request $request){
        /* @var Server $server */
        $server = Server::findOrFail($request->get('server'));

        $user = $this->parseUser($request->get('user'));
        $group = $request->get('group');

        $server->getPermissionManager()->removeUserFromGroup($user->uuid, $group);

        return Response::json([
            'success' => true,
            'message' => 'Вы успешно удалили игрока ' .$user->login . ' из группы ' . $group
        ]);
    }

    public function user_addgroup(Request $request){
        /* @var Server $server */
        $server = Server::findOrFail($request->get('server'));

        $user = $this->parseUser($request->get('user'));
        $group = $request->get('group');
        $time = $request->has('time') ? $request->get('time') : 0;

        $server->getPermissionManager()->addUserToGroup($user->uuid, $group, $time);

        return Response::json([
            'success' => true,
            'message' => 'Вы успешно добавили игрока ' .$user->login . ' в группу ' . $group
        ]);
    }

    public function group_setdefault(Request $request){
        /* @var Server $server */
        $server = Server::findOrFail($request->get('server'));

        $group = $request->get('group');

        $server->getPermissionManager()->deleteGroup($group);
        $server->getPermissionManager()->createGroup($group, true);

        return Response::json([
            'success' => true,
            'message' => 'Вы успешно сделали группу ' . $group . ' по умолчанию!'
        ]);
    }

    public function group_parent(Request $request){
        /* @var Server $server */
        $server = Server::findOrFail($request->get('server'));

        $group = $request->get('group');
        $parent = $request->get('parent');

        $server->getPermissionManager()->setParentToGroup($group, $parent);

        return Response::json([
            'success' => true,
            'message' => 'Вы успешно унаследовали группу ' . $group . ' от группы ' . $parent
        ]);
    }

    public function user_clear(Request $request)
    {
        /* @var Server $server */
        $server = Server::findOrFail($request->get('server'));

        $user = $this->parseUser($request->get('user'));

        $server->getPermissionManager()->clearUser($user->uuid);

        return Response::json([
            'success' => true,
            'message' => 'Вы успешно очистили все привилегии игрока ' . $user->login
        ]);
    }

    public function group_clear(Request $request){
        /* @var Server $server */
        $server = Server::findOrFail($request->get('server'));

        $group = $request->get('group');

        $users = $server->getPermissionManager()->playersInGroup($group);
        foreach ($users as $user){
            $server->getPermissionManager()->removeUserFromGroup($user->uuid, $group);
        }

        return Response::json([
            'success' => true,
            'message' => 'Вы успешно удалили всех игроков из группы ' . $group
        ]);
    }

    public function group_create(Request $request){
        /* @var Server $server */
        $server = Server::findOrFail($request->get('server'));

        $group = $request->get('group');
        $parent = $request->get('parent');
        $default = $request->get('default');

        $server->getPermissionManager()->createGroup($group, $default);
        if($parent) $server->getPermissionManager()->setParentToGroup($group, $parent);

        return Response::json([
            'success' => true,
            'message' => 'Вы успешно создали группу ' . $group
        ]);
    }

    public function group_delete(Request $request){
        /* @var Server $server */
        $server = Server::findOrFail($request->get('server'));

        $group = $request->get('group');

        $server->getPermissionManager()->deleteGroup($group);

        return Response::json([
            'success' => true,
            'message' => 'Вы успешно удалили группу ' . $group
        ]);
    }

    public function user_addperm(Request $request){
        /* @var Server $server */
        $server = Server::findOrFail($request->get('server'));

        $perm = $request->get('perm');

        $user = $this->parseUser($request->get('user'));

        $server->getPermissionManager()->addPermissionToUser($user->uuid, $perm);

        return Response::json([
            'success' => true,
            'message' => 'Вы успешно добавили игроку ' . $user->login . ' право ' . $perm
        ]);
    }

    public function user_removeperm(Request $request){
        /* @var Server $server */
        $server = Server::findOrFail($request->get('server'));

        $perm = $request->get('perm');

        $user = $this->parseUser($request->get('user'));

        $server->getPermissionManager()->removePermissionFromUser($user->uuid, $perm);

        return Response::json([
            'success' => true,
            'message' => 'Вы успешно забрали у игрока ' . $user->login . ' право ' . $perm
        ]);
    }

    public function group_addperm(Request $request){
        //TODO
    }

    public function group_removeperm(Request $request){
        //TODO
    }

    public function user_setprefix(Request $request){
        /* @var Server $server */
        $server = Server::findOrFail($request->get('server'));

        $user = $this->parseUser($request->get('user'));
        $prefix = $request->get('prefix');

        $server->getPermissionManager()->setUserPrefix($user->uuid, $prefix);

        return Response::json([
            'success' => true,
            'message' => 'Вы успешно обновили префикс игрока ' . $user->login
        ]);
    }

    public function user_removeprefix(Request $request){
        /* @var Server $server */
        $server = Server::findOrFail($request->get('server'));

        $user = $this->parseUser($request->get('user'));

        $server->getPermissionManager()->removeUserPrefix($user->uuid);

        return Response::json([
            'success' => true,
            'message' => 'Вы успешно удалили префикс игрока ' . $user->login
        ]);
    }

    public function user_setsuffix(Request $request){
        /* @var Server $server */
        $server = Server::findOrFail($request->get('server'));

        $user = $this->parseUser($request->get('user'));
        $prefix = $request->get('suffix');

        $server->getPermissionManager()->setUserSuffix($user->uuid, $prefix);

        return Response::json([
            'success' => true,
            'message' => 'Вы успешно обновили суффикс игрока ' . $user->login
        ]);
    }

    public function user_removesuffix(Request $request){
        /* @var Server $server */
        $server = Server::findOrFail($request->get('server'));

        $user = $this->parseUser($request->get('user'));

        $server->getPermissionManager()->removeUserSuffix($user->uuid);

        return Response::json([
            'success' => true,
            'message' => 'Вы успешно удалили суффикс игрока ' . $user->login
        ]);
    }

    public function group_setprefix(Request $request){
        /* @var Server $server */
        $server = Server::findOrFail($request->get('server'));

        $group = $request->get('group');
        $prefix = $request->get('prefix');

        $server->getPermissionManager()->setGroupPrefix($group, $prefix);

        return Response::json([
            'success' => true,
            'message' => 'Вы успешно обновили префикс группы ' . $group
        ]);
    }

    public function group_removeprefix(Request $request){
        /* @var Server $server */
        $server = Server::findOrFail($request->get('server'));

        $group = $request->get('group');

        $server->getPermissionManager()->removeGroupPrefix($group);

        return Response::json([
            'success' => true,
            'message' => 'Вы успешно удалили префикс группы ' . $group
        ]);
    }

    public function group_setsuffix(Request $request){
        /* @var Server $server */
        $server = Server::findOrFail($request->get('server'));

        $group = $request->get('group');
        $prefix = $request->get('suffix');

        $server->getPermissionManager()->setGroupSuffix($group, $prefix);

        return Response::json([
            'success' => true,
            'message' => 'Вы успешно обновили суффикс группы ' . $group
        ]);
    }

    public function group_removesuffix(Request $request){
        /* @var Server $server */
        $server = Server::findOrFail($request->get('server'));

        $group = $request->get('group');

        $server->getPermissionManager()->removeGroupSuffix($group);

        return Response::json([
            'success' => true,
            'message' => 'Вы успешно удалили суффикс группы ' . $group
        ]);
    }
}