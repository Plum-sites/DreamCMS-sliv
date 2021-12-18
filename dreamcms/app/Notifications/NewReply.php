<?php

namespace App\Notifications;

use App\Models\Forum\Discussion;
use App\Models\Forum\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewReply extends Notification
{
    use Queueable;

    public $discussion;
    public $post;
    public $user;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Discussion $discussion, Post $post)
    {
        $this->discussion = $discussion->only('title', 'slug');
        $this->post = $post->only('created_at');
        $this->user = $post->user->only('id', 'login', 'uuid');
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
            'discussion' => $this->discussion,
            'post' => $this->post,
            'user' => $this->user,
        ];
    }
}
