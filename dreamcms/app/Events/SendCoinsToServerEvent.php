<?php

namespace App\Events;

use App\Models\Server;
use App\Models\User;

class SendCoinsToServerEvent
{
    public $user;
    public $sum;
    public $server;

    public function __construct(User $user, Server $server, $sum)
    {
        $this->user = $user;
        $this->server = $server;
        $this->sum = $sum;
    }
}
