<?php

namespace App\Telegram;

use App\Models\Activity;
use App\Models\User;
use NotificationChannels\Telegram\TelegramMessage;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class LoginCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "login";

    /**
     * @var string Command Description
     */
    protected $description = "Подключить аккаунт к профилю";

    /**
     * @inheritdoc
     */
    public function handle()
    {
        $this->replyWithChatAction(['action' => Actions::TYPING]);

        $cache = \Cache::store('global');
        $tg_id = $this->getUpdate()->getMessage()->getFrom()->getId();

        $user = User::where('telegram_id', $tg_id)->first();

        if (!$user){
            $user_id = $cache->get('tglink_' . $this->getArguments()[0]);
            if ($user_id){
                $user = User::find($user_id);

                if ($user){
                    $user->telegram_data = $this->getUpdate()->getMessage()->getFrom();
                    $user->telegram_id = $tg_id;
                    $user->save();

                    Activity::user_action($user, 'tg_link', ['id' => $tg_id]);

                    $this->replyWithMessage([
                        'text' => "Привет <b>" . $user->login . "</b>! Ты успешно подключил свой аккаунт."
                            . PHP_EOL
                            . PHP_EOL
                            . "Теперь ты сможешь получать уведомления о входе в свой аккаунт, восстанавливать пароль через бота, самым первым узнавать о скидках и получать уникальные промокоды!",
                        'parse_mode' => 'HTML',
                        'disable_web_page_preview' => true
                    ]);
                }else{
                    $this->replyWithMessage(['text' => "Не удалось найти пользователя :("]);
                }
            }else{
                $this->replyWithMessage(['text' => "Данный код уже не действует :("
                    . PHP_EOL
                    . "Открой <a href='".url('/cabinet/security')."'>настройки безопасности</a> своего аккаунта, нажми на кнопку <b>'Подключить Telegram'</b>"
                ]);
            }
        }else{
            $this->replyWithMessage(['text' => "К вашему Telegram уже подключен аккаунт " . $user->login]);
        }
    }
}