<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Integration;
use App\Notifications\SuccessLoginNotify;
use App\Providers\TelegramProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use SocialiteProviders\Manager\OAuth2\User;
use Illuminate\Support\Facades\Password;

class IntegrationController extends Controller {

    public static $providers = [
        'telegram'  => TelegramProvider::class,
        'vkontakte' => \SocialiteProviders\VKontakte\Provider::class,
        'discord'   => \SocialiteProviders\Discord\Provider::class,
        'yandex'   => \SocialiteProviders\Yandex\Provider::class,
        //'mailru'   => \SocialiteProviders\Mailru\Provider::class,
        'steam'     => \App\Providers\SteamProvider::class
    ];

    public static $methods = [
        'link', 'login', 'recovery'
    ];

    public static function getRedirectURL($driver, $method){
        $config = config('services.' . $driver);
        $config['redirect'] = config('app.url') . '/oauth/' . $method . '/' . $driver;

        $provider = Socialite::buildProvider(self::$providers[$driver], $config)->stateless();

        return $provider->redirect()->getTargetUrl();
    }

    public function linkURL(Request $request, $driver){
        return [
            'success' => true,
            'url' => self::getRedirectURL($driver, 'link')
        ];
    }

    public function unlink(Request $request, $driver){
        if (Str::lower($driver) === 'telegram'){
            return [
                'success' => false,
                'message' => 'Отвязать Telegram можно только у бота!'
            ];
        }

        return [
            'success' => false
        ];
    }

    public function getToken(Request $request){
        $driver = $request->get('driver');

        /* @var $auth_user \App\Models\User */
        $auth_user = Auth::user();

        if ($auth_user){
            $integration = Integration::where([
                'user_id' => $auth_user->id,
                'driver' => $driver
            ])->first();

            if ($integration){
                return json_decode($integration->data);
            }else{
                return [
                    'success' => false,
                    'message' => 'Интеграция не найдена!'
                ];
            }
        }
    }

    public function register(Request $request, $driver){
        $config = config('services.' . $driver);
        $config['redirect'] = config('app.url') . '/oauth/register/' . $driver;

        /* @var $user User */
        $user = Socialite::buildProvider(self::$providers[$driver], $config)->stateless()->user();

        /* @var $integration Integration */
        $integration = Integration::where([
            'ext_id' => $user->getId(),
            'driver' => $driver
        ])->first();

        if ($integration){
            return [
                'success' => false,
                'message' => 'Этот аккаунт уже связан с другим пользователем'
            ];
        }

        /* @var $auth_user \App\Models\User */
        $auth_user = \App\Models\User::create([
            'name' => $user->getName(),
            'email' => $driver . ':' . $user->getEmail(),
            'password' => Hash::make(Str::random(32)),
            'avatar' => $user->getAvatar(),
            'driver' => $driver
        ]);

        Integration::create([
            'user_id' => $auth_user->id,
            'ext_id' => $user->getId(),
            'driver' => $driver,
            'data' => json_encode(array_merge($user->accessTokenResponseBody, $user->getRaw()))
        ]);

        return [
            'token' => $auth_user->createToken('register')->plainTextToken
        ];
    }

    public function link(Request $request, $driver){
        $config = config('services.' . $driver);
        $config['redirect'] = config('app.url') . '/oauth/link/' . $driver;

        $auth_user = Auth::user();

        /* @var $user User */
        $user = Socialite::buildProvider(self::$providers[$driver], $config)->stateless()->user();

        /* @var $integration Integration */
        $integration = Integration::where([
            'ext_id' => $user->getId(),
            'driver' => $driver
        ])->first();

        if ($integration){
            return [
                'success' => false,
                'message' => 'Этот аккаунт уже связан с пользователем!'
            ];
        }

        Integration::create([
            'user_id' => $auth_user->id,
            'ext_id' => $user->getId(),
            'driver' => $driver,
            'data' => json_encode($this->getData($user))
        ]);

        Activity::user_action($auth_user, 'integration_link', [
            'ip' => $request->ip(),
            'driver' => $driver,
            'data' => $this->getData($user)
        ]);

        return [
            'success' => true,
            'message' => 'Аккаунт успешно привязан!'
        ];
    }

    private function getData(User $user){
        return [
            'accessTokenResponseBody' => $user->accessTokenResponseBody,
            'user_raw' => $user->getRaw(),
            'avatar' => $user->getAvatar(),
            'email' => $user->getEmail(),
            'user_id' => $user->getId(),
            'nickname' => $user->getNickname(),
            'name' => $user->getName()
        ];
    }

    public function login(Request $request, $driver){
        $config = config('services.' . $driver);
        $config['redirect'] = config('app.url') . '/oauth/login/' . $driver;

        /* @var $user User */
        $user = Socialite::buildProvider(self::$providers[$driver], $config)->stateless()->user();

        /* @var $integration Integration */
        $integration = Integration::where([
            'ext_id' => $user->getId(),
            'driver' => $driver
        ])->first();

        if ($integration){
            $auth_user = \App\Models\User::find($integration->user_id);

            $integration->update([
                'data' => json_encode($this->getData($user))
            ]);

            try{
                $auth_user->notify(new SuccessLoginNotify($request, $driver));
            }catch (\Throwable $ignored){}

            return [
                'success' => true,
                'token' => \Auth::login($auth_user, true)
            ];
        }

        return [
            'success' => false,
            'message' => 'Этот аккаунт не связан с пользователем!'
        ];
    }

    public function recovery(Request $request, $driver){
        $config = config('services.' . $driver);
        $config['redirect'] = config('app.url') . '/oauth/recovery/' . $driver;

        /* @var $user User */
        $user = Socialite::buildProvider(self::$providers[$driver], $config)->stateless()->user();

        /* @var $integration Integration */
        $integration = Integration::where([
            'ext_id' => $user->getId(),
            'driver' => $driver
        ])->first();

        if ($integration){
            $auth_user = \App\Models\User::find($integration->user_id);

            $integration->update([
                'data' => json_encode($this->getData($user))
            ]);

            Activity::user_action($auth_user, 'integration_recovery', [
                'ip' => $request->ip(),
                'driver' => $driver,
                'data' => $this->getData($user)
            ]);

            return [
                'success' => true,
                'token' => Password::broker()->createToken($auth_user)
            ];
        }

        return [
            'success' => false,
            'message' => 'Этот аккаунт не связан с пользователем!'
        ];
    }
}
