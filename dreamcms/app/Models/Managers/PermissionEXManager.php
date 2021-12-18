<?php

namespace App\Models\Managers;

use App\Models\Interfaces\IDBPermissionManager;
use App\Models\User;
use Illuminate\Database\Connection;

class PermissionEXManager implements IDBPermissionManager{

    private $DB;

    function __construct(Connection $db){
        $this->DB = $db;
    }

    function listGroup()
    {
        return $this->DB->table('permissions_inheritance')
            ->select('child as name', 'parent')
            ->where([
                ['type', 0]
            ])->get();
    }

    function playerGroups($player)
    {
        $groups = $this->DB->table('permissions_inheritance')
            ->select('parent as group', 'child as uuid')
            ->where([
                ['child', $player],
                ['type', 1]
            ])->get();

        $groups->map(function ($item, $key){
            $expire = $this->DB->table('permissions')
                ->select('value as expire')
                ->where([
                    ['name', $item->uuid],
                    ['permission', 'group-'.$item->group.'-until'],
                    ['type', 1]
                ])->get()->pluck('expire')->first();
            if($expire) $item->expire = $expire;
            return $item;
        });

        return $groups;
    }

    function createGroup($group, $default = false){
        if($default){
            $this->DB->table('permissions_entity')->where([
                ['type', 0],
                ['default', 1]
            ])->delete();

            $this->DB->table('permissions_entity')->insert([
                'name' => $group,
                'type' => 0,
                'default' => 1
            ]);
        }else{
            $this->DB->table('permissions_entity')->where([
                ['type', 0],
                ['default', 1]
            ])->delete();
        }
    }

    function deleteGroup($group){
        $this->DB->table('permissions_entity')->where([
            ['type', 0],
            ['name', $group]
        ])->delete();
    }

    function setParentToGroup($group, $parent = null){
        $this->DB->table('permissions_inheritance')->where([
            ['child', $group],
            ['type', 0]
        ])->delete();

        if($parent){
            $this->DB->table('permissions_inheritance')->insert([
                'child' => $group,
                'parent' => $parent,
                'type' => 0
            ]);
        }
    }

    function addUserToGroup($user, $group, $until = 0){
        $this->DB->table('permissions_inheritance')->insert([
            'child' => $user,
            'parent' => $group,
            'type' => 1
        ]);
        if ($until > 0){
            $this->DB->table('permissions')->insert([
                'name' => $user,
                'type' => 1,
                'permission' => 'group-' . $group . '-until',
                'value' => $until
            ]);
        }
    }

    function removeUserFromGroup($user, $group){
        $this->DB->table('permissions')->where([
            ['name', $user],
            ['permission', 'group-' . $group . '-until'],
            ['type', 1]
        ])->delete();

        $this->DB->table('permissions_inheritance')->where([
            ['child', $user],
            ['parent', $group],
            ['type', 1]
        ])->delete();
    }

    function addPermissionToUser($user, $permission, $time = 0){
        $this->DB->table('permissions')->insert([
            'name' => $user,
            'type' => 1,
            'permission' => $permission
        ]);
    }

    function removePermissionFromUser($user, $permission){
        $this->DB->table('permissions')->where([
            ['name', $user],
            ['type', 1],
            ['permission', $permission]
        ])->delete();
    }

    function addPermissionToGroup($group, $permission, $time = 0){
        $this->DB->table('permissions')->insert([
            'name' => $group,
            'type' => 0,
            'permission' => $permission
        ]);
    }

    function removePermissionFromGroup($group, $permission){
        $this->DB->table('permissions')->where([
            ['name', $group],
            ['type', 0],
            ['permission', $permission]
        ])->delete();
    }

    function playersInGroup($group){
        $users = $this->DB->table('permissions_inheritance')
            ->select('child as uuid', 'parent as group')
            ->where([
                ['parent', $group],
                ['type', 1]
            ])->get();

        $users->map(function ($item, $key){
            $expire = $this->DB->table('permissions')
                ->select('value as expire')
                ->where([
                    ['name', $item->uuid],
                    ['permission', 'group-'.$item->group.'-until'],
                    ['type', 1]
                ])->get()->pluck('expire')->first();
            if($expire) $item->expire = $expire;
            return $item;
        });

        return $users;
    }

    function clearUser($user){
        if ($user instanceof User) $user = $user->uuid;

        $this->DB->table('permissions')->where([
            ['name', $user],
            ['type', 1]
        ])->delete();

        $this->DB->table('permissions_inheritance')->where([
            ['child', $user],
            ['type', 1]
        ])->delete();
    }

    //User prefix

    function getUserPrefix($user){
        if ($user instanceof User) $user = $user->uuid;

        return $this->DB->table('permissions')
        ->select('value as prefix')
        ->where([
            ['name', $user],
            ['type', 1],
            ['permission', 'prefix']
        ])->get()->pluck('prefix')->first();
    }

    function setUserPrefix($user, $prefix){
        if ($user instanceof User) $user = $user->uuid;

        $this->removeUserPrefix($user);

        $this->DB->table('permissions')->insert([
            'name' => $user,
            'type' => 1,
            'permission' => 'prefix',
            'value' => $prefix
        ]);
    }

    function removeUserPrefix($user){
        if ($user instanceof User) $user = $user->uuid;

        $this->DB->table('permissions')->where([
            ['name', $user],
            ['type', 1],
            ['permission', 'prefix']
        ])->delete();
    }

    //User suffix

    function getUserSuffix($user){
        if ($user instanceof User) $user = $user->uuid;

        return $this->DB->table('permissions')
            ->select('value as suffix')
            ->where([
                ['name', $user],
                ['type', 1],
                ['permission', 'suffix']
            ])->get()->pluck('suffix')->first();
    }

    function setUserSuffix($user, $prefix){
        $this->removeUserSuffix($user);

        if ($user instanceof User) $user = $user->uuid;

        $this->DB->table('permissions')->insert([
            'name' => $user,
            'type' => 1,
            'permission' => 'suffix',
            'value' => $prefix
        ]);
    }

    function removeUserSuffix($user){
        if ($user instanceof User) $user = $user->uuid;

        $this->DB->table('permissions')->where([
            ['name', $user],
            ['type', 1],
            ['permission', 'suffix']
        ])->delete();
    }

    //Group prefix

    function getGroupPrefix($user){
        return $this->DB->table('permissions')
            ->select('value as prefix')
            ->where([
                ['name', $user],
                ['type', 0],
                ['permission', 'prefix']
            ])->get()->pluck('prefix')->first();
    }

    function setGroupPrefix($user, $prefix){
        $this->removeGroupPrefix($user);
        $this->DB->table('permissions')->insert([
            'name' => $user,
            'type' => 0,
            'permission' => 'prefix',
            'value' => $prefix
        ]);
    }

    function removeGroupPrefix($user){
        $this->DB->table('permissions')->where([
            ['name', $user],
            ['type', 0],
            ['permission', 'prefix']
        ])->delete();
    }

    //Group suffix

    function getGroupSuffix($user){
        return $this->DB->table('permissions')
            ->select('value as suffix')
            ->where([
                ['name', $user],
                ['type', 0],
                ['permission', 'suffix']
            ])->get()->pluck('suffix')->first();
    }

    function setGroupSuffix($user, $prefix){
        $this->removeGroupSuffix($user);
        $this->DB->table('permissions')->insert([
            'name' => $user,
            'type' => 0,
            'permission' => 'suffix',
            'value' => $prefix
        ]);
    }

    function removeGroupSuffix($user){
        $this->DB->table('permissions')->where([
            ['name', $user],
            ['type', 0],
            ['permission', 'suffix']
        ])->delete();
    }

    //Permissions

    public function getUserPermissions($user){
        return $this->DB->table('permissions')
            ->select('permission')
            ->where([
                ['name', $user],
                ['type', 1]
            ])->get()->pluck('permission');
    }

    public function getGroupPermissions($group){
        return $this->DB->table('permissions')
            ->select('permission')
            ->where([
                ['name', $group],
                ['type', 0]
            ])->get()->pluck('permission');
    }
}