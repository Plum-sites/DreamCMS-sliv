<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\VerificatedTask;
use Request;

class VerifyController extends Controller{

    public function verify(Request $request, $key){
        $task = VerificatedTask::where([
            ['key', '=', $key],
            ['status', '=', 'waiting'],
        ])->get()->first();
        if($task){
            return $task->execute();
        }
        Request::session()->flash('alert-danger', 'Ссылка недействительна или вышел срок ожидания!');
        return redirect()->home();
    }

}