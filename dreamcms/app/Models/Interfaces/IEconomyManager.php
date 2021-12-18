<?php

namespace App\Models\Interfaces;

use Illuminate\Database\Connection;

interface IEconomyManager {
    function __construct(Connection $db);

    function getBalance($user);

    function addMoney($user, $count);
    function withdrawMoney($user, $count);
}