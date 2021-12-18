<?php

namespace App\Http\Middleware;

use Closure;

class Recaptcha
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */

    public function handle($request, Closure $next, $action)
    {
        $response = (new \ReCaptcha\ReCaptcha(config('recaptcha.private_key')))
            ->setExpectedAction($action)
            ->verify($request->input('captcha'), $request->ip());

        if (!$response->isSuccess() || $response->getScore() < 0.6) {
            return \Response::json([
                'success' => false,
                'message' => 'Сайт принял вас за робота! Проверьте свой компьютер на вирусы, попробуйте другой браузер или попробуйте позже!'
            ]);
        }

        return $next($request);
    }
}
