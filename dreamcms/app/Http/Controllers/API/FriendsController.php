<?php

namespace App\Http\Controllers\API;

use App;
use App\Http\Controllers\Controller;
use App\Models\User;
use Hootlex\Friendships\Models\Friendship;
use Hootlex\Friendships\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class FriendsController extends Controller
{
    public function requests(Request $request){
        $user = \Auth::user();

        return Response::json([
            'success' => true,
            'requests' => Friendship::whereRecipient($user)->whereStatus(Status::PENDING)->orderBy('created_at', 'DESC')->with('sender:id,login,uuid')->get()
        ]);
    }

    public function add(Request $request){
        $user = User::find($request->get('user'));

        if ($user->id == \Auth::id()){
            return Response::json([
                'success' => false,
                'message' => 'Вы не можете добавить в друзья себя!'
            ]);
        }

        if ($user->hasFriendRequestFrom(\Auth::user())){
            return Response::json([
                'success' => true,
                'message' => 'Вы уже отправляли заявку! Дождитесь ответа!'
            ]);
        }

        \Auth::user()->befriend($user);
        
        return Response::json([
            'success' => true,
            'message' => 'Заявка на добавление в друзья отправлена!'
        ]);
    }

    public function accept(Request $request){
        $user = User::find($request->get('user'));

        \Auth::user()->acceptFriendRequest($user);

        return Response::json([
            'success' => true,
            'message' => 'Вы успешно добавили ' . $user->login . ' в друзья!'
        ]);
    }

    public function deny(Request $request){
        $user = User::find($request->get('user'));

        \Auth::user()->denyFriendRequest($user);

        return Response::json([
            'success' => true,
            'message' => 'Вы оставили ' . $user->login . ' в подписчиках!'
        ]);
    }

    public function remove(Request $request){
        $user = User::find($request->get('user'));

        \Auth::user()->unfriend($user);

        return Response::json([
            'success' => true,
            'message' => 'Вы перенесли ' . $user->login . ' в подписчики!'
        ]);
    }
}
