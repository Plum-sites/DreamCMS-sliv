<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\User;
use App\Notifications\SuccessLoginNotify;
use App\SCEncryptor;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;
use Socialite;
use SocialiteProviders\VKontakte\Provider;
use Yadahan\AuthenticationLog\Listeners\LogSuccessfulLogin;

class LoginController extends Controller
{
    use ThrottlesLogins;

    protected function guard()
    {
        return Auth::guard('api');
    }

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout', 'vkRedirect');
    }

    public function username()
    {
        return 'login';
    }

    public function logout(Request $request)
    {
        User::disableAuditing();

        $this->guard()->logout();

        $request->session()->flush();

        $request->session()->regenerate();

        return redirect('/');
    }

    public function showLoginForm(Request $request)
    {
        return view('auth.login');
    }

    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            $this->username() => 'required|string',
            'password' => 'required|string'
        ]);
    }

    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }

    protected function attemptLogin(Request $request)
    {
        $remember = true;
        return $this->guard()->attempt(
            $this->credentials($request), $remember
        );
    }

    public function login(Request $request)
    {
        User::disableAuditing();

        $this->validateLogin($request);

        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $this->incrementLoginAttempts($request);

        if (filter_var($request->get($this->username()), FILTER_VALIDATE_EMAIL)){
            $user = User::fromEmail($request->get($this->username()));
            if ($user){
                $request = $request->merge([
                    'login' => $user->login
                ]);
            }
        }

        if ($token = $this->attemptLogin($request)) {
            $user = User::fromLogin($request->get('login'));

            $secret = $user->otp_secret ? SCEncryptor::decryptString($user->otp_secret) : false;

            if ($otp = $request->get('otp')){
                if($secret && $otp){
                    $google2fa = new Google2FA();
                    $backup_codes = $user->backup_codes ? json_decode(SCEncryptor::decryptString($user->backup_codes), true) : false;

                    $use_backupcode = $backup_codes && in_array($otp, $backup_codes);

                    if($google2fa->verifyKey($secret, $otp, 3) || $use_backupcode){
                        if ($use_backupcode && $backup_codes){
                            $user->backup_codes = SCEncryptor::encryptString(json_encode(collect($backup_codes)->filter(function ($bcode) use ($otp){
                                return $bcode != $otp;
                            })->values()->toArray()));
                        }

                        $user->save();

                        try{
                            $user->notify(new SuccessLoginNotify($request,$use_backupcode ? 'логин, пароль, резервный код' : 'логин, пароль, 2FA'));
                        }catch (\Throwable $ignored){}

                        $this->authenticated($request, $user, true);

                        return $this->sendLoginResponse($request, $token);
                    }else{
                        Activity::user_action($user, '2fa_fail', [
                            'ip' => $request->ip(),
                            'code' => $otp
                        ]);

                        //\Auth::logout();
                        return \Response::json([
                            'success' => false,
                            'message' => 'Неверный код 2FA!'
                        ]);
                    }
                }
            }

            if($secret){
                //\Auth::logout();

                return \Response::json([
                    'success' => true,
                    'otp' => true
                ]);
            }

            try{
                $user->notify(new SuccessLoginNotify($request));
            }catch (\Throwable $ignored){}

            $this->authenticated($request, $user, false);

            return $this->sendLoginResponse($request, $token);
        }

        return $this->sendFailedLoginResponse($request);
    }

    protected function authenticated(Request $request, $user, $otp = false)
    {
        $logger = new LogSuccessfulLogin($request);

        $logger->handle(new \Illuminate\Auth\Events\Login(
            $this->guard(),
            $user,
            $otp
        ));

        //event();
    }

    protected function sendLoginResponse(Request $request, $token)
    {
        return \Response::json([
            'success' => true,
            'token' => $token
        ]);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        return \Response::json([
            'success' => false,
            'message' => 'Неверный логин или пароль!'
        ]);
    }
}
