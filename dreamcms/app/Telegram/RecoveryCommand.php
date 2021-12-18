<?php

namespace App\Telegram;

use App\Models\User;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Support\Facades\Password;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class RecoveryCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "recovery";

    /**
     * @var string Command Description
     */
    protected $description = "Восстановление пароля";

    /**
     * @inheritdoc
     */
    public function handle()
    {
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        $tg_id = $this->getUpdate()->getMessage()->getFrom()->getId();

        $user = User::where('telegram_id', $tg_id)->first();

        if ($user){

            $broker = Password::broker();

            if ($broker instanceof PasswordBroker){
                /* @var $broker PasswordBroker */
                $link = config('app.url') . '/password/reset/' . $broker->createToken($user);

                $this->replyWithMessage([
                    'text' => "Я сгенерировал тебе уникальную ссылку для восстановления пароля."
                        . PHP_EOL
                        . PHP_EOL
                        . "<a href='" . $link . "'>Сбросить пароль</a>",
                    'parse_mode' => 'HTML',
                    'disable_web_page_preview' => true
                ]);
            }else{
                $this->replyWithMessage(['text' => "Восстановление пароля через бота временно недоступно!"]);
            }
        }else{
            $this->replyWithMessage(['text' => "Для начала, тебе необходимо подключить игровой аккаунт!"]);
        }
    }
}