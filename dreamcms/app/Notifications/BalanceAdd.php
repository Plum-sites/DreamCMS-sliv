<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BalanceAdd extends Notification
{
    use Queueable;

    public $sum;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($sum)
    {
        $this->sum = $sum;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'sum' => $this->sum,
        ];
    }
}
