<?php

namespace App\Models\Interfaces;

use Illuminate\Database\Connection;

interface IDBPermissionManager extends IPermissionManager{
    function __construct(Connection $db);

    function listGroup();
    function playerGroups($user);
    function playersInGroup($group);

}