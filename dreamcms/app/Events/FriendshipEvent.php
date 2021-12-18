<?php

namespace App\Events;

use Hootlex\Friendships\Models\Friendship;

class FriendshipEvent
{
    public $friendship;

    public function __construct(Friendship $friendship)
    {
        $this->friendship = $friendship;
    }
}
