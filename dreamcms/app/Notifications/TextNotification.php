<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TextNotification extends Notification
{
    use Queueable;

    public $text;
    public $url;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($text, $url = null)
    {
        $this->text = $text;
        $this->url = $url;
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
            'text' => $this->text,
            'route' => $this->url
        ];
    }
}
