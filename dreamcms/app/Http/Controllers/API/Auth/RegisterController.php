<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\TextNotification;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Webpatser\Uuid\Uuid;

class RegisterController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'login' => 'required|max:16|min:3|unique:users|regex:/^[a-zA-Z0-9_]+$/',
            'email' => 'required|max:255|unique:users|email:rfc,dns',
            'password' => 'required|min:6|max:40',
        ], [
            'unique' => 'Этот :attribute уже занят!',
        ]);
    }

    public function register(Request $request)
    {
        if ($this->validator($request->all())->fails()){
           $errors = $this->validator($request->all())->errors();

           return \Response::json([
               'success' => false,
               'message' => implode('<br>', $errors->all(':message'))
           ]);
        }

        if (preg_match( '/\p{Cyrillic}/u', $request->get('password'))){
            return \Response::json([
                'success' => false,
                'message' => 'Пароль может содержать только латинские буквы и символы!'
            ]);
        }

        if (!self::checkPassword($request->get('password'))){
            return \Response::json([
                'success' => false,
                'message' => 'Этот пароль найден в базе утекших паролей, либо слишком простой!'
            ]);
        }

        event(new Registered($user = $this->create($request)));

        $token = auth('api')->login($user);

        $user->notify(new TextNotification("Поздравляем с успешной регистрацией!"));

        return \Response::json([
            'success' => true,
            'token' => $token
        ]);
    }

    public static function checkPassword($password, $score = FALSE){
        try{
            $CRACKLIB = "cracklib-check";

            $out = [];
            $ret = NULL;
            $command = "echo " . escapeshellarg($password) . " | {$CRACKLIB}";

            exec($command, $out, $ret);

            if(($ret == 0) && preg_match("/: ([^:]+)$/", $out[0], $regs)) {
                list(, $msg) = $regs;
                switch($msg)
                {
                    case "OK":
                        return true;
                        break;

                    default:
                        return false;
                }
            }
        }catch (\Throwable $ignored){
            \Log::error($ignored);
        }

        return true;
    }

    public function checkLogin(Request $request){
        $validator = Validator::make($request->all(), [
            'login' => 'required|max:16|min:3|unique:users|regex:/^[a-zA-Z0-9_]+$/'
        ]);

        if ($validator->fails()){
            $errors = $validator->errors();

            return \Response::json([
                'success' => false,
                'errors' => $errors
            ]);
        }

        return \Response::json([
            'success' => true
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(Request $request)
    {
        $data = [
            'login' => $request->get('login'),
            'email' => $request->get('email'),
            'uuid' => Uuid::generate()->string,
            'password' => bcrypt($request->get('password')),
            'reg_time' => time(),
            'reg_ip' => $request->ip(),
            'passchange_time' => Carbon::now()->getTimestamp(),
            'realmoney' => 0,
            'money' => 0
        ];

        if ($request->has('refer')){
            $refer = User::fromLogin($request->get('refer'));
            if (!$refer){
                $refer = User::find(intval($request->get('refer')));
            }

            if ($refer){
                $data['refer'] = $refer->id;
            }
        }

        return User::create($data);
    }

    public function showRegistrationForm(Request $request)
    {
        if($refer = $request->get('p')){
            $request->session()->flash('refer', $refer);
        }
        
        return view('auth.register');
    }
}
