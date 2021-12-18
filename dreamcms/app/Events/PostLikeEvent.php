<?php

namespace App\Events;

use App\Models\Forum\Post;
use App\Models\User;

class PostLikeEvent
{
    public $post;
    public $from;
    public $to;
    public $reputation;

    public function __construct(Post $post, User $from, User $to, $reputation = 0)
    {
        $this->post = $post;
        $this->from = $from;
        $this->to = $to;
        $this->reputation = $reputation;
    }
}
