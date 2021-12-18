<?php

namespace App\Models\Managers;

use App\Models\Interfaces\IDBPermissionManager;
use App\Models\User;
use Illuminate\Database\Connection;

class MiniGamesPermissionManager implements IDBPermissionManager{

    private $DB;

    function __construct(Connection $db){
        $this->DB = $db;
    }

    function clearUser($user){
        if ($user instanceof User) $user = $user->login;

        $this->DB->table('profiles')->where([
            'name' => $user
        ])->update([
            'group' => 'default'
        ]);
    }

    function createGroup($group, $default = false){}
    function deleteGroup($group){}

    function setParentToGroup($group, $parent = null){}

    function addUserToGroup($user, $group, $time = 0){
        if ($user instanceof User)
            $user = $user->login;
        else
            $user = User::fromUUID($user)->login;

        $this->DB->table('profiles')->where([
            'name' => $user
        ])->update([
            'group' => $group . ':' . $time
        ]);

        $this->DB->table('group_transactions')->insert([
            'name' => $user,
            'value' => $group . ':' . $time
        ]);
    }

    function removeUserFromGroup($user, $group){
        $this->DB->table('profiles')->where([
            'name' => $user
        ])->update([
            'group' => 'default'
        ]);
    }

    function addPermissionToUser($user, $permission, $time = 0){}
    function removePermissionFromUser($user, $permission){}

    function addPermissionToGroup($group, $permission, $time = 0){}
    function removePermissionFromGroup($group, $permission){}

    function setUserPrefix($user, $prefix){
        if ($user instanceof User) $user = $user->login;
        else $user = User::fromUUID($user)->login;

        $this->DB->table('profiles')->where([
            'name' => $user
        ])->update([
            'prefix' => $prefix
        ]);
    }

    function removeUserPrefix($user){
        if ($user instanceof User) $user = $user->login;
        else $user = User::fromUUID($user)->login;

        $this->DB->table('profiles')->where([
            'name' => $user
        ])->update([
            'prefix' => null
        ]);
    }

    function setUserSuffix($user, $prefix){}
    function removeUserSuffix($user){}

    function setGroupPrefix($group, $prefix){}
    function removeGroupPrefix($group){}

    function setGroupSuffix($group, $prefix){}
    function removeGroupSuffix($group){}

    function listGroup(){
        return ['vip', 'premium', 'deluxe', 'legend'];
    }

    function playerGroups($user){
        if ($user instanceof User) $user = $user->login;
        else $user = User::fromUUID($user)->login;

        try{
            $group = $this->DB->table('profiles')
                ->select('group')
                ->where([
                    ['name', $user]
                ])->get()->first()->group;
            if (substr_count($group, ':')){
                $info = explode(':', $group);
                if ($info[1] < time()) return null;
                $group = $info[0];
            }
            return [$group];
        }catch (\Exception $exception){
            return ['default'];
        }
    }

    function playersInGroup($group){

    }

}