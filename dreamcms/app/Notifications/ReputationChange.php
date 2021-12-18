<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ReputationChange extends Notification
{
    use Queueable;

    public $user;
    public $reputation;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(User $user, $reputation)
    {
        $this->user = $user->only('id', 'uuid', 'login');
        $this->reputation = $reputation;
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
            'user' => $this->user,
            'reputation' => $this->reputation
        ];
    }
}
