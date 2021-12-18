<?php

namespace App\Models\Managers;

use App\Models\Interfaces\IDBPermissionManager;
use Illuminate\Database\Connection;

class LuckPermsManager implements IDBPermissionManager{

    private $DB;

    function __construct(Connection $db){
        $this->DB = $db; 
    }

    function listGroup()
    {
        return $this->DB->table('luckperms_groups')
            ->select('name')
            ->get();
    }

    function playerGroups($player)
    {
        $groups = $this->DB->table('luckperms_players')
            ->select('primary_group as group', 'uuid')
            ->where([
                ['uuid', $player]
            ])->get();

        $groups->map(function ($item, $key){
            $expire = $this->DB->table('luckperms_user_permissions')
                ->select('expiry as expire')
                ->where([
                    ['uuid', $item->uuid],
                    ['permission', 'group.'.$item->group]
                ])->get()->pluck('expire')->first();
            if($expire) $item->expire = $expire;
            return $item;
        });

        return $groups;
    }

    function createGroup($group, $default = false){
        $this->DB->table('luckperms_groups')->insert([
            'name' => $group
        ]);
    }

    function deleteGroup($group){
        $this->DB->table('luckperms_groups')->where([
            ['name', $group]
        ])->delete();
    }

    function setParentToGroup($group, $parent = null){
        //TODO
    }

    function addUserToGroup($user, $group, $until = 0){
        $this->DB->table('luckperms_players')
        ->where([
            ['uuid', $user]
        ])
        ->update([
            'primary_group' =>  $group
        ]);

        $this->DB->table('luckperms_user_permissions')->insert([
            'uuid' => $user,
            'permission' => 'group.' . $group,
            'value' => 1,
            'server' => 'global',
            'world' => 'global',
            'expiry' => $until,
            'contexts' => '{}'
        ]);
    }

    function removeUserFromGroup($user, $group){
        $this->DB->table('luckperms_players')
        ->where([
            ['uuid', $user]
        ])
        ->update([
            'primary_group' =>  'default'
        ]);

        $this->DB->table('luckperms_user_permissions')->where([
            ['uuid', $user],
            ['permission', 'group.' . $group]
        ])->delete();
    }

    function addPermissionToUser($user, $permission, $time = 0){
        //TODO
    }

    function removePermissionFromUser($user, $permission){
        //TODO
    }

    function addPermissionToGroup($group, $permission, $time = 0){
        //TODO
    }

    function removePermissionFromGroup($group, $permission){
        //TODO
    }

    function playersInGroup($group){
        $users = $this->DB->table('luckperms_players')
            ->select('uuid', 'primary_group as group')
            ->where([
                ['primary_group', $group]
            ])->get();

        $users->map(function ($item, $key){
            $expire = $this->DB->table('luckperms_user_permissions')
                ->select('expiry as expire')
                ->where([
                    ['uuid', $item->uuid],
                    ['permission', 'group.'.$item->group]
                ])->get()->pluck('expire')->first();
            if($expire) $item->expire = $expire;
            return $item;
        });

        return $users;
    }

    function clearUser($user){
        $this->DB->table('luckperms_players')->where([
            ['uuid', $user]
        ])->delete();

        $this->DB->table('luckperms_user_permissions')->where([
            ['uuid', $user]
        ])->delete();
    }

    //User prefix

    function getUserPrefix($user){
        //TODO
    }

    function setUserPrefix($user, $prefix){
        //TODO
    }

    function removeUserPrefix($user){
        //TODO
    }

    //User suffix

    function getUserSuffix($user){
        //TODO
    }

    function setUserSuffix($user, $prefix){
        //TODO
    }

    function removeUserSuffix($user){
        //TODO
    }

    //Group prefix

    function getGroupPrefix($user){
        //TODO
    }

    function setGroupPrefix($user, $prefix){
        //TODO
    }

    function removeGroupPrefix($user){
        //TODO
    }

    //Group suffix

    function getGroupSuffix($user){
        //TODO
    }

    function setGroupSuffix($user, $prefix){
        //TODO
    }

    function removeGroupSuffix($user){
        //TODO
    }

    //Permissions

    public function getUserPermissions($user){
        return $this->DB->table('luckperms_user_permissions')
            ->select('permission')
            ->where([
                ['uuid', $user]
            ])->get()->pluck('permission');
    }

    public function getGroupPermissions($group){
        return $this->DB->table('luckperms_group_permissions')
            ->select('permission')
            ->where([
                ['name', $group]
            ])->get()->pluck('permission');
    }
}