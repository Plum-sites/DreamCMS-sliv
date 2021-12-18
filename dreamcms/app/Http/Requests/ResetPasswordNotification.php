<?php

namespace App\Http\Requests;

use Illuminate\Auth\Notifications\ResetPassword as ResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword implements ShouldQueue
{
    use Queueable;

    /**
     * Build the mail representation of the notification.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->line([
                'Вы получили это письмо, потому что запросили восстановление пароля.',
                'Нажмите на кнопку ниже, что бы сбросить ваш пароль:',
            ])
            ->action('Сменить пароль', url('/password/reset', $this->token))
            ->line('Если вы не запрашивали измения, ничего делать не нужно. Ваш аккаунт в безопасности :)');
    }
}
