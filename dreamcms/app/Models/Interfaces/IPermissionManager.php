<?php

namespace App\Models\Interfaces;

interface IPermissionManager {
    function clearUser($user);

    function createGroup($group, $default = false);
    function deleteGroup($group);

    function setParentToGroup($group, $parent = null);

    function addUserToGroup($user, $group, $time = 0);
    function removeUserFromGroup($user, $group);

    function addPermissionToUser($user, $permission, $time = 0);
    function removePermissionFromUser($user, $permission);

    function addPermissionToGroup($group, $permission, $time = 0);
    function removePermissionFromGroup($group, $permission);

    function setUserPrefix($user, $prefix);
    function removeUserPrefix($user);

    function setUserSuffix($user, $prefix);
    function removeUserSuffix($user);

    function setGroupPrefix($group, $prefix);
    function removeGroupPrefix($group);

    function setGroupSuffix($group, $prefix);
    function removeGroupSuffix($group);
}