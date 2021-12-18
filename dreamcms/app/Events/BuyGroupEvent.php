<?php

namespace App\Events;

use App\Models\DonateGroup;
use App\Models\Server;
use App\Models\User;

class BuyGroupEvent
{
    public $user;
    public $group;
    public $server;
    public $renew;
    public $upgrade;

    public function __construct(User $user, DonateGroup $group, Server $server, $renew = false, $upgrade = false)
    {
        $this->user = $user;
        $this->group = $group;
        $this->server = $server;
        $this->renew = $renew;
        $this->upgrade = $upgrade;
    }
}
