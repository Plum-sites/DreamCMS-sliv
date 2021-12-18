<?php

namespace App\Policies;

use App\Http\Controllers\API\Admin\ExtendedPermissions;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

class ExtendedPolicy
{
    public function handle(User $user, $modelobj_or_prefix, $perm)
    {
        if ($user && $user->isSuperAdmin()){
            return true;
        }
        
        if ($modelobj_or_prefix instanceof Model){
            return ExtendedPermissions::checkModelAccess($user, get_class($modelobj_or_prefix), $perm, $modelobj_or_prefix->id);
        }elseif (is_string($modelobj_or_prefix)){
            //try {
                return $user->hasPermissionTo($modelobj_or_prefix . '.' . $perm);
            //}catch (PermissionDoesNotExist $exception){}

            //return false;
        }
    }
}