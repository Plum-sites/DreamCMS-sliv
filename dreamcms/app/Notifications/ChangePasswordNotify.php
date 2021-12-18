<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;
use NotificationChannels\Telegram\TelegramChannel;

class ChangePasswordNotify extends Notification
{
    use Queueable;

    public $ip;
    public $userAgent;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->ip = $request->ip();
        $this->userAgent = $request->userAgent();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', TelegramChannel::class];
    }

    /* @var $user User */
    public function toTelegram($user)
    {
        return TelegramMessage::create()
            ->to($user->routeNotificationForTelegram())
            ->content("Успешная *смена пароля*!\nМы обнаружили успешную смену пароля на аккаунте *" . $user->login . "*. \n\nIP адрес: *" . $this->ip . "*\n\nЕсли это были вы, проигнорируйте это сообщение. Если же у вас есть подозрения, что аккаунт был взломан, смените пароль и сообщите администрации!");
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
