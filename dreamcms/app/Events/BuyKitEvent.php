<?php

namespace App\Events;

use App\Models\User;

class BuyKitEvent
{
    public $user;
    public $kit;

    public function __construct(User $user, $kit)
    {
        $this->user = $user;
        $this->kit = $kit;
    }
}
