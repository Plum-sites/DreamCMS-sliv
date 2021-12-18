<?php

namespace App\Events;

use App\Models\User;

class PaymentEvent
{
    public $user;
    public $sum;

    public function __construct(User $user, $sum)
    {
        $this->user = $user;
        $this->sum = $sum;
    }
}
