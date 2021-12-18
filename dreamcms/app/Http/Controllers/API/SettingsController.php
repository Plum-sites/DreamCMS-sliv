<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\Controller;
use App\Mail\EmailConfirm;
use App\Models\Activity;
use App\Models\Integration;
use App\Models\User;
use App\Notifications\ChangePasswordNotify;
use App\SCEncryptor;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;
use Socialite;
use SocialiteProviders\VKontakte\Provider;
use Validator;
use Wkhooy\ObsceneCensorRus;

class SettingsController extends Controller
{
    public function index(){
        $user = Auth::user();

        $otp = !empty($user->otp_secret);

        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        $tempsecret = $google2fa->generateSecretKey();

        return \Response::json([
            'otp' => $otp,
            'sign' => $user->sign ? $user->sign : '',
            'tempsecret' => $otp ? null : $tempsecret,
            'qr_url' => $otp ? null : self::getQRCodeGoogleUrl($google2fa->getQRCodeUrl(
                config('app.name'),
                $user->login,
                $tempsecret
            )),
            'lastPasswordChange' => $user->passchange_time,
            'auth_logs' => $user->authentications()->get(),

            'integrations' => Integration::where([
                'user_id' => $user->id
            ])->get(),

            'drivers' => array_keys(IntegrationController::$providers)
        ]);
    }

    public static function getQRCodeGoogleUrl($url, $size = 200)
    {
        return self::generateGoogleQRCodeUrl('https://chart.googleapis.com/', 'chart', 'chs='.$size.'x'.$size.'&chld=M|0&cht=qr&chl=', $url);
    }

    public static function generateGoogleQRCodeUrl($domain, $page, $queryParameters, $qrCodeUrl)
    {
        $url = $domain .
            rawurlencode($page) .
            '?' . $queryParameters .
            urlencode($qrCodeUrl);

        return $url;
    }

    public function confirmEmailToken(Request $request){
        $token = $request->get('token');

        $attemp = \DB::table('email_confirmation')->where([
            ['token', '=', $token],
            ['time', '>', Carbon::now()->getTimestamp() - (24 * 60 * 60)]
        ])->first();

        if ($attemp){
            $user = User::find($attemp->user_id);

            if ($user){
                if ($user->email_confirmed_at){
                    return [
                        'success' => false,
                        'message' => 'У этого игрока уже подтверждена почта!'
                    ];
                }

                $email = $attemp->email;

                if (User::where([
                    ['email', '=', $email],
                    ['email_confirmed_at', '>', 0]
                ])->count()){
                    return [
                        'success' => false,
                        'message' => 'Данная почта уже подтверждена у другого игрока!'
                    ];
                }

                $old_user = User::fromEmail($email);
                if ($old_user && $old_user->id !== $user->id){
                    $old_user->email = $old_user->login . '_' . $user->login .'@mail.com';
                    $old_user->save();
                }

                $user->email = $email;
                $user->email_confirmed_at = Carbon::now()->getTimestamp();
                $user->save();

                Activity::user_action($user, 'email_confirm', [
                    'ip' => $request->ip(),
                    'email' => $email,
                    'old_user' => $old_user ? $old_user->id : null
                ]);

                \DB::table('email_confirmation')->where('id', '=', $attemp->id)->delete();

                \Cache::delete('core_user_' . $user->id);

                return [
                    'success' => true,
                    'message' => 'Вы успешно подтвердили свою почту!'
                ];
            }else{
                return [
                    'success' => false,
                    'message' => 'Пользователь не найден!'
                ];
            }
        }

        return [
            'success' => false,
            'message' => 'Токен устарел или не найден! Запросите новое подтверждение!'
        ];
    }

    public function takeBonus(Request $request){
        return [
            'success' => false,
            'message' => 'Акция закончена!'
        ];

        /*
        $user = Auth::user();
        $server = Server::findOrFail($request->get('server'));
        if ($user->email_confirmed_at){
            if ($user->otp_secret){
                if (Activity::where([
                    ['actor_id', '=', $user->id],
                    ['action', '=', 'take_email2fa_bonus']
                ])->count()){
                    return [
                        'success' => false,
                        'message' => 'Вы уже забирали бонус!'
                    ];
                }

                Activity::user_action($user, 'take_email2fa_bonus', [
                    'server' => $server->id
                ]);

                $activegroups = UserGroup::where([
                    ['user_id', '=', $user->id],
                    ['time', '<', time()],
                    ['expire', '>', time()],
                    ['server_id', '=', $server->id]
                ])->get();

                $renewal = false;

                foreach ($activegroups as $usergroup){
                    $renewal = true;

                    $usergroup->expire = $usergroup->expire + (7 * 24 * 60 * 60);
                    $usergroup->save();

                    $server->getPermissionManager()->addUserToGroup($user->uuid, $usergroup->getDonateGroup()->pexname, $usergroup->expire);
                }

                $dg = DonateGroup::findOrFail(1);

                if ($renewal){
                    return [
                        'success' => true,
                        'message' => 'Ваш донат на этом сервере продлен на 7 дней!'
                    ];
                }else{
                    $usergroup = new UserGroup;
                    $usergroup->user_id = $user->id;
                    $usergroup->server_id = $server->id;
                    $usergroup->group_id = $dg->id;
                    $usergroup->time = time();
                    $usergroup->expire = time() + (7 * 24 * 60 * 60);
                    $usergroup->save();

                    $server->getPermissionManager()->addUserToGroup($user->uuid, $dg->pexname, $usergroup->expire);

                    return [
                        'success' => true,
                        'message' => 'Вам выдан VIP на 7 дней!'
                    ];
                }

            }else{
                return [
                    'success' => false,
                    'message' => 'У вас не подключена двухэтапная авторизация!'
                ];
            }
        }else{
            return [
                'success' => false,
                'message' => 'У вас не подтверждена почта!'
            ];
        }*/
    }

    public function confirmEmail(Request $request){
        $user = Auth::user();

        if ($user->email_confirmed_at){
            return [
                'success' => false,
                'message' => 'У вас уже подтверждена почта!'
            ];
        }

        $attemps = \DB::table('email_confirmation')->where([
            ['user_id', '=', $user->id],
            ['time', '>', Carbon::now()->getTimestamp() - (24 * 60 * 60)],
        ])->count();

        if ($attemps >= 3){
            return [
                'success' => false,
                'message' => 'Вы не можете запросить более 3-ех подтверждений за 24 часа!'
            ];
        }

        $email = $request->get('email');

        $validator = Validator::make([
            'email' => $email
        ], [
            'email' => 'required|email:rfc,dns'
        ]);

        if ($validator->fails()){
            return [
                'success' => false,
                'message' => 'Невалидный Email адрес!'
            ];
        }

        if (User::where([
            ['email', '=', $email],
            ['email_confirmed_at', '>', 0]
        ])->count()){
            return [
                'success' => false,
                'message' => 'Этот EMail уже подтвержден другим игроком!'
            ];
        }

        $token = Str::random(32);

        \DB::table('email_confirmation')->insert([
            'user_id' => $user->id,
            'time' => Carbon::now()->getTimestamp(),
            'token' => $token,
            'email' => $email
        ]);

        \Mail::to($email)->send(new EmailConfirm($user, $email, $token));

        return [
            'success' => true,
            'message' => 'Проверьте указанный почтовый ящик. Мы направили Вам письмо с подтверждением!'
        ];
    }

    private function change_password(Request $request){
        $password = $request->get('pass');
        $new_password = $request->get('newpass');
        $drop_sessions = $request->get('logout');

        /* @var User $user */
        $user = Auth::user();

        if (Hash::check($password, $user->password)){
            if (!RegisterController::checkPassword($new_password)){
                return [
                    'success' => false,
                    'message' => 'Новый пароль найден в базе утекших паролей, либо слишком простой!'
                ];
            }

            $user->update([
                'password' => Hash::make($new_password)
            ]);

            $user->password = Hash::make($new_password);
            $user->passchange_time = Carbon::now()->getTimestamp();
            $user->save();

            $user->clearCoreCache();

            Activity::user_action($user, 'changepass', ['ip' => $request->ip(), 'drop_sessions' => $drop_sessions]);

            $user->notify(new ChangePasswordNotify($request));

            if ($drop_sessions){
                $user->token_reset = now()->getTimestamp();
            }

            return [
                'success' => true,
                'message' => 'Вы успешно сменили пароль!'
            ];
        }else{
            return [
                'success' => false,
                'message' => 'Неверный текущий пароль!'
            ];
        }
    }

    private function enable_otp(Request $request){
        User::disableAuditing();
        
        $secret = $request->get('tempsecret');
        $code = trim(str_replace(' ', '', $request->get('code')));

        if (Auth::user()->otp_secret){
            return [
                'success' => false,
                'message' => 'У вас уже включена OTP авторизация!'
            ];
        }

        $google2fa = new Google2FA();

        if ($google2fa->verifyKey($secret, $code, 4)){
            $user = Auth::user();
            $user->otp_secret = SCEncryptor::encryptString($secret);

            Activity::user_action(Auth::user(), 'enableotp', [
                'ip' => $request->ip()
            ]);

            $backup_codes = collect();
            for ($i = 0; $i < 10; $i++){
                $backup_codes->push(Str::random(8) . '-' . Str::random(8) . '-' . Str::random(8));
            }

            $user->backup_codes = SCEncryptor::encryptString(json_encode($backup_codes->toArray()));

            $user->save();

            return [
                'success' => true,
                'message' => 'Вы успешно включили двухэтапную авторизацию!',
                'codes' => $backup_codes
            ];
        }

        return [
            'success' => false,
            'message' => 'Неверный код! Попробуйте еще раз.'
        ];
    }

    private function disable_otp(Request $request){
        User::disableAuditing();
        
        $code = trim(str_replace(' ', '', $request->get('code')));
        $google2fa = new Google2FA();

        $user = Auth::user();

        $secret = $user->otp_secret ? SCEncryptor::decryptString($user->otp_secret) : false;

        if (!$secret){
            return [
                'success' => false,
                'message' => 'У вас не подключена 2FA!'
            ];
        }

        $backup_codes = $user->backup_codes ? json_decode(SCEncryptor::decryptString($user->backup_codes), true) : false;

        if ($google2fa->verifyKey($secret, $code, 4) || ($backup_codes && in_array($code, $backup_codes))){
            $user->otp_secret = null;
            $user->backup_codes = null;

            /*if ($backup_codes){
                $user->backup_codes = SCEncryptor::encryptString(json_encode(collect($backup_codes)->filter(function ($bcode) use ($code){
                    return $bcode != $code;
                })->values()->toArray()));
            }*/

            $user->save();

            Activity::user_action(Auth::user(), 'disableotp', [
                'ip' => $request->ip()
            ]);

            return [
                'success' => true,
                'message' => 'Вы успешно отключили двухэтапную авторизацию!'
            ];
        }

        return [
            'success' => false,
            'message' => 'Неверный код!'
        ];
    }

    public function profile(Request $request){
        User::disableAuditing();

        $user = Auth::user();

        if ($request->has('sign')){
            return $this->sign($request);
        }

        if ($request->has('show_signs')){
            $user->show_signs = $request->get('show_signs');
            $user->save();
        }

        if ($request->has('hide_friends')){
            $user->hide_friends = $request->get('hide_friends');
            $user->save();
        }

        \Cache::delete('core_user_' . $user->id);

        return \Response::json([
           'success' => true,
           'message' => 'Вы успешно обновили информацию профиля!'
        ]);
    }

    public function save(Request $request){
        User::disableAuditing();

        if ($request->get('reset_sessions')){
            $user = Auth::user();
            $user->token_reset = now()->getTimestamp();

            $user->save();

            return [
                'success' => true,
                'message' => 'Вы успешно сбросили все сессии!'
            ];
        }

        if ($request->has('newpass')){
            if($answer = $this->change_password($request)){
                return \Response::json($answer);
            }
        }

        if ($request->has('code') && $request->get('tempsecret')){
            if($answer = $this->enable_otp($request)){
                return \Response::json($answer);
            }
        }

        if ($request->has('code')){
            if($answer = $this->disable_otp($request)){
                return \Response::json($answer);
            }
        }
    }

    public function sign(Request $request){
        $user = Auth::user();

        if ($user->isBanned()){
            return [
                'success' => false,
                'message' => 'Забаненные игроки не могут менять подпись на форуме!'
            ];
        }

        $sign = $request->get('sign');

        if (!ObsceneCensorRus::isAllowed(strip_tags($sign))){
            return [
                'success' => false,
                'message' => 'Запрещено использовать мат!'
            ];
        }

        $sign = \Purifier::clean($sign, 'forum_sign');

        if (!mb_check_encoding($sign, "UTF-8")){
            return [
                'success' => false,
                'message' => 'В подписи нельзя использовать спец-символы, в том числе смайлики!'
            ];
        }

        $user->sign = $sign;
        $user->save();

        \Cache::delete('core_user_' . $user->id);

        return [
            'success' => true,
            'message' => 'Вы успешно установили подпись!'
        ];
    }
}