<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;


class ResetPasswordController extends Controller
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

    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required',
            'password' => 'required|min:8',
        ];
    }
    public function reset(Request $request)
    {
        User::disableAuditing();

        if (substr_count($request->get('email'), '@') <= 0){
            $user = User::fromLogin($request->get('email'));
            if (!$user){
                return [
                    'success' => false,
                    'message' => 'Игрок с данным логином или EMail не найден!'
                ];
            }
            $request->request->set('email', $user->email);
        }

        $this->validate($request, $this->rules());

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $response = $this->broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'), function ($user, $password) use ($request) {
                $this->resetPassword($user, $password);

                Activity::user_action($user, 'reset_password', [
                    'type' => 'email',
                    'ip' => $request->ip()
                ]);
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $response == Password::PASSWORD_RESET
            ? $this->sendResetResponse($request, $response)
            : $this->sendResetFailedResponse($request, $response);
    }

    protected function resetPassword($user, $password)
    {
        /* @var User $user */
        $user->forceFill([
            'password' => Hash::make($password),
            'passchange_time' => Carbon::now()->getTimestamp()
        ])->save();

        $user->clearCoreCache();

        event(new PasswordReset($user));
    }

    protected function sendResetResponse(Request $request, $response)
    {
        return \Response::json([
            'success' => true,
            'message' => 'Вы успешно сменили пароль! Теперь вы можете авторизоваться.'
        ]);
    }

    protected function sendResetFailedResponse(Request $request, $response)
    {
        return \Response::json([
            'success' => false,
            'message' => 'Не удалось сменить пароль, возможно вы неверно указали почту!'
        ]);
    }

    public function broker()
    {
        return Password::broker();
    }
}
