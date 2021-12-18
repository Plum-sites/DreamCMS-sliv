<?php

namespace App\Notifications;

use Hootlex\Friendships\Models\Friendship;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class FriendRequest extends Notification
{
    use Queueable;

    public $friendship;

    public $action;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Friendship $friendship, $action)
    {
        $this->friendship = $friendship->loadMissing('sender:id,login,uuid')->loadMissing('recipient:id,login,uuid');
        $this->action = $action;
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
            'friendship' => $this->friendship,
            'action' => $this->action,
        ];
    }
}
