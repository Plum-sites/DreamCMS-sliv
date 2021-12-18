<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\ModerEntry;
use App\Models\User;
use App\Notifications\TextNotification;
use Carbon\Carbon;
use Gate;
use Illuminate\Http\Request;
use OwenIt\Auditing\Models\Audit;

class ModerEntryController extends Controller {

    public function logs(Request $request){
        $entry = ModerEntry::findOrFail($request->get('id'));

        $logs = Audit::where([
            ['auditable_id', '=', $entry->id],
            ['auditable_type', '=', ModerEntry::class],
        ])->get();

        $logs = $logs->map(function ($log){
            $log->admin = User::find($log->user_id)->login;
            return $log;
        });

        return [
            'success' => true,
            'logs' => $logs
        ];
    }

    public function search(Request $request){
        if (Gate::denies('crud', [ModerEntry::class, 'view'])){
            abort(403, 'Недостаточно прав!');
        }

        $time = $request->get('time');
        $start = Carbon::createFromTimestamp($time['start']);
        $end = Carbon::createFromTimestamp($time['end']);
    
        $user = User::find($request->get('user'));
        $status = $request->get('status');
    
        $where = [
            ['time', '>=', $start->getTimestamp()],
            ['time', '<=', $end->getTimestamp()]
        ];
        
        if ($user) $where[] = ['user_id', '=', $user->id];
        if ($status) $where[] = ['status', '=', $status];
        
        $list = \DB::table('moder_requests')->where($where)->get();
        
        $rows = [];
        foreach ($list as $entry){
            $rows[] = [
                'id' => $entry->id,
                'fio' => $entry->fio,
                'user' => User::find($entry->user_id)->login,
                'server' => $entry->server,
                'old' => $entry->old,
                'city' => $entry->city,
                'contacts' => $entry->contacts,
                'date' => Carbon::createFromTimestamp($entry->time)->toDateTimeString(),
                'status' => $entry->status,
                'about' => $entry->about
            ];
        }
        
        return \Response::json([
            'columns' => [
                ['label' => '', 'field' => '', 'sortable' => false],
                ['label' => 'Игрок', 'field' => 'user'],
                ['label' => 'ФИО', 'field' => 'fio'],
                ['label' => 'Сервер', 'field' => 'server'],
                ['label' => 'Возраст', 'field' => 'old'],
                ['label' => 'Город', 'field' => 'city'],
                ['label' => 'Дата заполнения', 'field' => 'date'],
                ['label' => 'Статус', 'field' => 'status'],
                ['label' => 'Действия', 'field' => '', 'sortable' => false]
            ],
            'rows' => $rows
        ]);
    }
    
    public function updateStatus(Request $request){
        if (Gate::denies('crud', [ModerEntry::class, 'edit'])){
            abort(403, 'Недостаточно прав!');
        }
        
        $status = $request->get('status');

        /* @var $entry ModerEntry*/
        $entry = ModerEntry::findOrFail($request->get('id'));
        $entry->status = $status;
        $entry->save();

        $entry->user()->first()->notify(new TextNotification("Статус вашей заявки на модератора изменен! Нажмите для просмотра.", "moderentry"));
        
        return \Response::json([
            'success' => true,
            'message' => 'Вы успешно обновили статус!'
        ]);
    }
    
    public function updateComment(Request $request){
        if (Gate::denies('crud', [ModerEntry::class, 'edit'])){
            abort(403, 'Недостаточно прав!');
        }
        
        $comment = $request->get('text');
        
        $entry = ModerEntry::findOrFail($request->get('id'));
        $entry->answer = $comment;
        
        $entry->save();
    
        return \Response::json([
            'success' => true,
            'message' => 'Вы успешно обновили комментарий!'
        ]);
    }
    
}
