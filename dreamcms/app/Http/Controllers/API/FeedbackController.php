<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ModerEntry;
use App\Models\Server;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Validator;

class FeedbackController extends Controller
{
    public function index(){
        return view('feedback.index');
    }

    public function moder(){
        if ($entry = ModerEntry::getCurrent(\Auth::user())){
            $entry->can_delete = $entry->time > Carbon::now()->addHours(-3)->getTimestamp();

            return new JsonResponse([
               'current' => $entry
            ]);
        }

        return new JsonResponse([
            'current' => null
        ]);
    }

    public function moder_delete(){
        /* @var $entry ModerEntry */
        if ($entry = ModerEntry::getCurrent(\Auth::user())){
            $entry->delete();

            return \Response::json([
                'success' => true,
                'message' => 'Вы успешно удалили заявку!'
            ]);
        }

        return \Response::json([
            'success' => false,
            'message' => 'Заявка не найдена!'
        ]);
    }

    public function moder_accept(Request $request){
        if (ModerEntry::getCurrent($request->user())){
            return \Response::json([
                'success' => false,
                'message' => 'Вы не можете подать сейчас заявку, подождите неделю с отправки предыдущей!'
            ]);
        }

        $validator = Validator::make($request->all(), [
            'fio' => 'required|max:255',
            'age' => 'required|max:3',
            'city' => 'required|max:255',
            'contacts' => 'required|max:255',
            'server' => 'required',
            'about' => 'required|min:150|max:3000'
        ]);

        if (!$validator->fails()){
            if (!mb_check_encoding($request->get('about'), "UTF-8")){
                return \Response::json([
                    'success' => false,
                    'message' => 'В заявке нельзя использовать спец-символы, в том числе смайлики!'
                ]);
            }

            ModerEntry::create([
                'fio' => $request->get('fio'),
                'old' => $request->get('age'),
                'city' => $request->get('city'),
                'contacts' => $request->get('contacts'),
                'server' => Server::findOrFail($request->get('server'))->name,
                'about' => $request->get('about'),
                'time' => time(),
                'user_id' => $request->user()->id
            ]);

            return \Response::json([
                'success' => true,
                'message' => 'Заявка успешно отправлена! Проверяйте статус на этой странице! В среднем в течении 2-х недель, вам придет ответ.'
            ]);
        }else{
            return \Response::json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
}
