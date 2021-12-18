<?php

namespace App\Policies;

use App\Http\Controllers\API\Admin\ExtendedPermissions;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class CrudPolicy
{
    public function handle(User $user, $model, $action, $id = false)
    {
        if ($user && $user->isSuperAdmin()){
            return true;
        }
    
        $class = $model;
        
        if ($model instanceof Model){
            $class = get_class($model);
        }
    
        return ExtendedPermissions::checkModelAccess($user, $class, $action);
    }
}