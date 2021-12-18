<?php

namespace App\Policies;

use App\Models\User;
use Backpack\PermissionManager\app\Models\Permission;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

class ForumPolicy
{
    public function handle(User $user, $model, $action, $param = false)
    {
        try{
            $permission = Permission::findByName('forum.' . $model . '.' . $action);
            if ($user != null && $user->hasPermissionTo($permission)){
                return true;
            }
        }catch (PermissionDoesNotExist $e){
            abort(403, 'Право "' . 'forum.' . $model . '.' . $action . '" не существует!');
        }

        return false;
    }
}