<?php

namespace App\Models;

use App\Mail\VerifyTask;
use Eloquent;
use Request;

class VerificatedTask extends Eloquent
{
    protected $table = 'verification_tasks';
    public $timestamps = false;

    protected $fillable = [
        'user_id', 'key', 'type', 'task', 'params', 'status', 'created', 'executed'
    ];

    public static function createTask(User $user, $type, $task, $params = array()){
        $send_task = VerificatedTask::create([
            'user_id' => $user->id,
            'key' => str_random(32),
            'type' => $type,
            'status' => 'waiting',
            'task' => $task,
            'params' => json_encode($params),
            'created' => time()
        ]);
        switch ($type){
            case 'email':
                \Mail::to($user)->queue(new VerifyTask($send_task));
                break;
            default:
                break;
        }
    }

    public function view(){
        $params = json_decode($this->params, true);
        switch ($this->task){
            case 'send_realmoney':
                $title = 'Вы запросили перевод денег';
                $message = [$title, 'Для перевода ' . $params['sum'] . 'р. игроку ' . User::find($params['recipient'])->login . ' пройдите по ссылке ниже:'];
                $button = 'Нажмите сюда для перевода';
                break;
            default:
                return [];
                break;
        }

        return [
            'level' => 'info',
            'actionUrl' => url('/verify/' . $this->key),
            'introLines' => $message,
            'actionText' => $button,
            'outroLines' => []
        ];
    }

    public function execute(){
        $params = json_decode($this->params, true);
        switch ($this->task){
            case 'send_realmoney':
                $sum = $params['sum'];
                $user_id = $params['recipient'];
                $sender = User::find($this->user_id);
                if($sender->withdrawRealmoney($sum)){
                    $this->status = 'executed';
                    $this->executed = time();
                    if($this->save()){
                        $user = User::find($user_id);
                        $user->addRealmoney($sum);

                        Activity::user_action($sender, 'sendplayer', [
                            'getter' => $user->id,
                            'count' => $sum,
                            'comment' => $params['comment']
                        ]);

                        Request::session()->flash('alert-success', 'Вы успешно перевели игроку ' . $user->login . ' сумму ' . $sum . ' руб.');

                        return redirect()->home();
                    }else{
                        Request::session()->flash('alert-danger', 'Ошибка при сохранении!');
                        return redirect()->home();
                    }
                }else{
                    Request::session()->flash('alert-danger', 'Недостаточно денег на балансе для перевода!');
                    return redirect()->home();
                }
                break;
            default:
                Request::session()->flash('alert-danger', 'Ошибка при выполнении!');
                return redirect()->home();
                break;
        }
    }
}