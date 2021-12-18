<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class SubscriptionController extends Controller
{
    public function check(Request $request)
    {
        /* @var User $user */
        $user = User::fromUUID($request->get('uuid'));
        
        $sub = DB::table('subscriptions')->where('user_id', $user->id)->get()->first();
        
        if ($sub) {
            return 1;
        }else{
            return '§cВам необходимо приобрести подписку для игры на этом сервере!';
        }
    }

    public function index()
    {
        /* @var User $user */
        $user = Auth::user();

        $sub = DB::table('subscriptions')->where('user_id', $user->id)->get()->first();

        return view('subscription.index', ['subscription' => $sub]);
    }

    public function buy(Request $request){
        /* @var User $user */
        $user = Auth::user();

        $sub = DB::table('subscriptions')->where('user_id', $user->id)->get()->first();

        if ($sub){
            return Response::json([
                'success' => false,
                'message' => 'У вас уже есть активная подписка на этот план!'
            ]);
        }else{
            if ($user->withdrawRealmoney(350)){

                DB::table('subscriptions')->insert([
                    'user_id' => $user->id,
                    'name' => 'nano7',
                    'start' => Carbon::now()->getTimestamp(),
                    'end' => Carbon::now()->addDays(30)->getTimestamp(),
                ]);

                return Response::json([
                    'success' => true,
                    'message' => 'Вы успешно приобрели подписку!'
                ]);
            }else{
                return Response::json([
                    'success' => false,
                    'message' => 'Недостаточно средств!'
                ]);
            }
        }
    }
}