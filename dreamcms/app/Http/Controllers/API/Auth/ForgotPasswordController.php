<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Socialite;
use SocialiteProviders\VKontakte\Provider;

class ForgotPasswordController extends Controller
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

    public function sendResetLinkEmail(Request $request)
    {
        if (substr_count($request->get('email'), '@') <= 0){
            $user = User::fromLogin($request->get('email'));

            if(!$user){
                return [
                    'success' => false,
                    'message' => 'Аккаунт с таким логином или EMail адресом не найден! '
                ];
            }

            $request->request->set('email', $user->email);
        }

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = Password::sendResetLink(
            $request->only('email')
        );

        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($request, $response)
            : $this->sendResetLinkFailedResponse($request, $response);
    }

    protected function sendResetLinkResponse(Request $request, $response)
    {
        return [
            'success' => true,
            'message' => 'Ссылка на восстановление пароля отправлена Вам на почту!'
        ];
    }

    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return [
            'success' => false,
            'message' => 'Аккаунт с таким логином или EMail адресом не найден! ' . $response
        ];
    }

    public function showLinkRequestForm(Request $request)
    {
        return view('auth.passwords.email');
    }

    public function broker()
    {
        return Password::broker();
    }
}
