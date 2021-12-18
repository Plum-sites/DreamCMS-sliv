<?php

namespace App\Telegram;

use App\Models\User;
use NotificationChannels\Telegram\TelegramMessage;
use Telegram\Bot\Actions;
use Telegram\Bot\Commands\Command;

class StartCommand extends Command
{
    /**
     * @var string Command Name
     */
    protected $name = "start";

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

        $tg_id = $this->getUpdate()->getMessage()->getFrom()->getId();

        $user = User::where('telegram_id', $tg_id)->first();

        if (!$user){
            $this->replyWithMessage([
                'text' => "Привет. Я бот <b>" . config('app.name') ."</b>! Сейчас я помогу тебе подключить твой аккаунт Telegram к игровому профилю."
                . PHP_EOL
                . PHP_EOL
                . "Открой <a href='".url('/cabinet/security')."'>настройки безопасности</a> своего аккаунта, нажми на кнопку <b>'Подключить Telegram'</b>",
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true
            ]);
            $this->replyWithMessage(['text' => "Пришли мне пожалуйста команду, которую ты там увидишь. Не забывай, что она действует всего 5 минут!"]);
        }else{
            $this->replyWithMessage(['text' => "Привет, " . $user->login . "!"]);
        }
    }
}