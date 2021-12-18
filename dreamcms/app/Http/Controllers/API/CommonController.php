<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Server;
use App\Models\User;
use App\SCEncryptor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use Purifier;
use Wkhooy\ObsceneCensorRus;


class CommonController extends Controller
{
    public function filterText(Request $request){
        $text = $request->get('t');

        $text = ObsceneCensorRus::getFiltered(Purifier::clean($text));

        return new JsonResponse([
            'filtered' => $text
        ]);
    }

    public function otpCheck(Request $request){
        $token = trim($request->get('token'));

        if (!in_array($token, $this->getActiveTokens())){
            return new JsonResponse([
                'success' => false,
                'message' => [
                    'Неверный токен! Обратитесь к администрации!'
                ]
            ], 403);
        }

        $uuid = $request->get('uuid');

        $user = User::fromUUID($uuid);

        if ($user){
            $secret = $user->otp_secret ? SCEncryptor::decryptString($user->otp_secret) : false;

            if ($request->has('code')){
                $code = $request->get('code');

                $google2fa = new Google2FA();
                if ($google2fa->verifyKey($secret, $code, 3)){
                    return new JsonResponse([
                        'success' => true
                    ]);
                }

                return new JsonResponse([
                    'success' => false
                ]);
            }else{
                return new JsonResponse([
                    'success' => true,
                    'enabled' => $secret ? true : false
                ]);
            }
        }else{
            return new JsonResponse([
                'success' => false,
                'message' => [
                    'Пользователь не найден! Обратитесь к администрации!'
                ]
            ], 403);
        }
    }

    public function contest(Request $request){
        $token = trim($request->get('token'));

        if (!in_array($token, $this->getActiveTokens())){
            return new JsonResponse([
                'success' => false,
                'message' => [
                    'Неверный токен! Обратитесь к администрации!'
                ]
            ], 403);
        }
        
        $uuid = $request->get('uuid');

        $user = User::fromUUID($uuid);

        if ($user){
            if (\DB::table('contest')->where('uuid', '=', $user->uuid)->count() > 0){
                return new JsonResponse([
                    'success' => false,
                    'message' => [
                        'Вы уже участвуете в розыгрыше!'
                    ]
                ]);
            }else{
                \DB::table('contest')->insert([
                    'uuid' => $user->uuid,
                    'token' => $token,
                    'time' => now()
                ]);

                return new JsonResponse([
                    'success' => true,
                    'message' => [
                        'Ура! Ваш ник ' . $user->login . ' добавлен в список участников!',
                        'Следите за новостями в нашей группе ВКонтакте'
                    ]
                ]);
            }
        }else{
            return new JsonResponse([
                'success' => false,
                'message' => [
                    'Пользователь не найден! Обратитесь к администрации!'
                ]
            ], 403);
        }
    }

    public function getActiveTokens(){
        $tokens = collect();

        foreach (Server::getActive() as $server){
            if ($server->api_token && is_string($server->api_token) && !empty($server->api_token)){
                $tokens->push($server->api_token);
            }
        }

        return $tokens->toArray();
    }

    public function snooper(Request $request){
        $data = $request->except('token', 'id');

        $user = User::fromUUID($request->get('uuid'));

        if ($user && $user->accessToken === $request->get('token')){
            $record = \DB::table('snooper')->where($data)->first();

            if ($record){
                \DB::table('snooper')->where([
                    'id' => $record->id
                ])->update([
                    'updated_at' => now()
                ]);
            }else{
                $data = array_merge($data, [
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                \DB::table('snooper')->insert($data);
            }

            return [
                'success' => true
            ];
        }

        return [
            'success' => false,
            'message' => 'Пользователь не найден или сессия устарела!'
        ];
    }
}
