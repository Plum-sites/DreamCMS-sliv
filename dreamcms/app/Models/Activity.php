<?php

namespace App\Models;

use Carbon\Carbon;
use Eloquent;

class Activity extends Eloquent
{
    protected $table = 'activity';

    public $timestamps = false;

    protected $fillable = [
        'actor_type', 'actor_id', 'action', 'time', 'params'
    ];

    public static function user_action(User $user, $action, $params = array(), $time = false){
        if(!$time) $time = time();
        Activity::create([
            'actor_type' => 'user',
            'actor_id' => $user->id,
            'action' => $action,
            'time' => $time,
            'params' => json_encode($params)
        ]);
    }

    public static function list_actions(User $user, $action, $where = []){
        return Activity::where(array_merge([
            'actor_type' => 'user',
            'actor_id' => $user->id,
            'action' => $action
        ], $where))->get();
    }

    public static function actions_date($action, $start = false, $end = false, $where = []){
        if (!$start) $start = Carbon::now()->startOfDay()->getTimestamp();
        if (!$end) $end = Carbon::now()->endOfDay()->getTimestamp();

        return Activity::where(array_merge([
            ['action', '=', $action],
            ['time', '>', $start],
            ['time', '<', $end]
        ], $where))->get();
    }

    public static function user_logs(User $user, $start = false, $end = false, $where = []){
        if (!$start) $start = Carbon::now()->subDays(14)->getTimestamp();
        if (!$end) $end = Carbon::now()->getTimestamp();

        return Activity::where(array_merge([
            ['actor_id', '=', $user->id],
            ['time', '>', $start],
            ['time', '<', $end]
        ], $where))->orderByDesc('time')->get();
    }

    public function getTitle(){
        switch ($this->action){
            case 'unitpay_add':
                return 'Пополнение счета';
            case 'buygroup':
                return 'Покупка группы';
            case 'buyitem':
                return 'Покупка в магазине';
            case 'buy_unban':
                return 'Покупка разбана';
            case 'changepass':
                return 'Смена пароля';
            case 'enableotp':
                return 'Включение ОТР';
            case 'exchange':
                return 'Обмен валюты';
            case 'sendplayer':
                return 'Передача стримов';
            case 'sendserver':
                return 'Покупка стримов';
            case 'sendsite':
                return 'Перевод стримов';
            case 'setprefix':
                return 'Смена префикса';
            default:
                return 'Неизвестное действие';
        }
    }

    public function getSubTitle(){
        $params = json_decode($this->params);
        switch ($this->action){
            case 'unitpay_add':
                return 'Сумма: ' . $params->orderSum;
            case 'buygroup':
                try{
                    return "Сервер: " . Server::find($params->server)->name;
                }catch (\Exception $exception){
                    try{
                        return "Сервер: " . $params->server->name;
                    }catch (\Exception $exception){
                        return "Сервер: скрытый";
                    }
                }
            case 'buyitem':
                return 'Магазин: ' . Shop::find($params->shop)->name;
            case 'buy_unban':
                return 'Цена: ' . $params->ban->price;
            case 'changepass':
                return 'Это очень хорошо';
            case 'enableotp':
                return 'Аккаунт защищен';
            case 'exchange':
                return 'Количество: ' . $params->count;
            case 'sendplayer':
                return 'Получатель: ' . User::find($params->getter)->login;
            case 'sendserver':
                return 'Кол-во: ' . $params->count;
            case 'sendsite':
                return 'Кол-во: ' . $params->count;
            case 'setprefix':
                return 'Префикс: ' . $params->prefix;
            default:
                return 'Ошибка';
        }
    }

    public function getMsg(){
        $params = json_decode($this->params);
        switch ($this->action){
            case 'unitpay_add':
                return 'Спасибо Вам за поддержку проекта!';
            case 'buygroup':
                return "Группа: " . $params->group;
            case 'buyitem':
                try{
                    return "Предмет: " . ShopItem::find($params->item)->name;
                }catch (\Exception $exception){
                    return "Предмет: " . json_encode($params->item);
                }
            case 'buy_unban':
                return 'Причина: ' . $params->ban->Reason;
            case 'changepass':
                return 'Меняйте пароль как можно чаще!';
            case 'enableotp':
                return 'Не удаляйте приложение до отключения OTP!';
            case 'exchange':
                return 'Курс: ' . $params->course;
            case 'sendplayer':
                return 'Кол-во: ' . $params->count .', комментарий: ' . $params->comment;
            case 'sendserver':
                return '';
            case 'sendsite':
                return '';
            case 'setprefix':
                return '';
            default:
                return 'Ошибка';
        }
    }
}
