<?php

namespace App\Models\Managers;

use App\Models\Interfaces\IEconomyManager;
use App\Models\User;
use Illuminate\Database\Connection;

class MiniGamesEconomyManager implements IEconomyManager{
    /* @var Connection $DB */
    private $DB;

    function __construct(Connection $db){
        $this->DB = $db;
    }

    function getBalance($user){
        if ($user instanceof User) $user = $user->login;

        try{
            return $this->DB->table('profiles')
                ->select('money')
                ->where('name', $user)
                ->get()->first()->money;
        }catch (\Exception $exception){
            return 0;
        }
    }

    function addMoney($user, $count){
        if ($user instanceof User) $user = $user->login;

        return $this->DB->table('transactions')->insert(['name' => $user, 'value' => $count]);
    }

    function withdrawMoney($user, $count){
        return false;
    }
}