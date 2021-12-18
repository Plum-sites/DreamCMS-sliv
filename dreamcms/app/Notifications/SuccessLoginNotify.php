<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;
use NotificationChannels\Telegram\TelegramChannel;

class SuccessLoginNotify extends Notification implements ShouldQueue
{
    use Queueable;

    public $ip;
    public $userAgent;
    public $method;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Request $request, $method = 'логин/пароль')
    {
        $this->ip = $request->ip();
        $this->userAgent = $request->userAgent();
        $this->method = $method;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [TelegramChannel::class];
    }

    public function toTelegram($user)
    {
        return TelegramMessage::create()
            ->to($user->routeNotificationForTelegram())
            ->content("Произведен *успешный* вход в аккаунт *" . $user->login . "* на сайте.\n\nМы обнаружили новый вход с IP адреса: *" . $this->ip . "*\nМетод входа: *" . $this->method . "*\n\nЕсли это были вы, проигнорируйте это сообщение. Если же у вас есть подозрения, что аккаунт был взломан, смените пароль и сообщите администрации!");
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
            'ip' => $this->ip,
            'userAgent' => $this->userAgent
        ];
    }
}
