<?php
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::any('/balance', "BalanceController@get");
Route::any('/balance/withdraw', "BalanceController@withdraw");
Route::any('/text/filter', "CommonController@filterText");
Route::any('/otp/check', "CommonController@otpCheck");
Route::any('/snooper', "CommonController@snooper");

Route::any('/contest/register', "CommonController@contest");

Route::any('/subscribe/check', "SubscriptionController@check");

Route::get('/verify/{string}', "VerifyController@verify")->name('verify');

// LEGACY
Route::post('/cabinet/buygroup', "ProfileController@cabbuygroup");

Route::post('/pay/skinpay', "\App\Http\Controllers\PaymentController@skinpay");
Route::post('/pay/enot', "\App\Http\Controllers\PaymentController@enot");
Route::post('/pay/obmenka', "\App\Http\Controllers\PaymentController@obmenka");

Route::prefix('game')->group(function (){
    Route::post('auth', '\App\Http\Controllers\GameController@auth');
    Route::post('load', '\App\Http\Controllers\GameController@load')->middleware('auth');
});

Route::prefix('core')->group(function (){
    Route::any('ip', 'CoreController@ip');

    Route::any('load', 'CoreController@loadInfo');
    Route::any('user/find', 'CoreController@findUser');

    Route::get('notifications', 'CoreController@getNotifications')->middleware('auth');
    Route::any('offers', 'CoreController@getOffers');
});

Route::prefix('oauth')->group(function (){
    Route::get('login/{string}', 'IntegrationController@login');
    Route::get('recovery/{string}', 'IntegrationController@recovery');
    Route::get('link/{string}', 'IntegrationController@link');
});

Route::prefix('forum')->namespace('Forum')->group(function (){
    Route::get('/', 'ChatterController@index');

    Route::get('latest', 'ChatterController@loadLatest');
    Route::get('leaders', 'ChatterController@loadLeaders');
    Route::get('populars', 'ChatterController@loadPopulars');

    Route::any('search', 'ChatterController@search');
    Route::any('admins', 'ChatterController@admins');

    Route::prefix('category')->group(function (){
        Route::get('{string}', 'ChatterController@loadCategory');
    });

    Route::prefix('discussion')->group(function (){
        Route::post('create', "ChatterDiscussionController@store")->middleware('auth');

        Route::post('unlock', "ChatterDiscussionController@unlock")->middleware('auth');
        Route::post('lock', "ChatterDiscussionController@lock")->middleware('auth');

        Route::post('move', "ChatterDiscussionController@move")->middleware('auth');
        Route::post('delete', "ChatterDiscussionController@delete")->middleware('auth');

        Route::post('pin', "ChatterDiscussionController@pin")->middleware('auth');
        Route::post('unpin', "ChatterDiscussionController@unpin")->middleware('auth');

        Route::get('{string}', 'ChatterDiscussionController@loadDiscussion');
    });

    Route::prefix('post')->group(function (){
        Route::post('template', "ChatterPostController@saveTemplate")->middleware('auth');
        Route::post('template/delete', "ChatterPostController@deleteTemplate")->middleware('auth');
        Route::post('create', "ChatterPostController@store")->middleware('auth');

        Route::post('update/{id}', "ChatterPostController@update")->middleware('auth');
        Route::post('delete/{id}', "ChatterPostController@destroy")->middleware('auth');
        Route::post('like/{id}', "ChatterPostController@like")->middleware('auth');
        Route::get('like/{id}', "ChatterPostController@getLikes");
    });

    Route::prefix('user')->group(function (){
        Route::post('ban', 'ChatterController@ban')->middleware('auth');
        Route::post('reputation', 'ChatterController@reputation')->middleware('auth');
        Route::post('sign/delete', 'ChatterController@sign_del')->middleware('auth');
    });
});

Route::prefix('page')->group(function (){
    Route::get('{string}', 'PageController@view');
});

Route::prefix('user')->group(function (){
    Route::get('{string}', 'ProfileController@view');
});

Route::prefix('auth')->group(function (){
    Route::post('login', 'Auth\LoginController@login')->middleware('captcha:login');
    Route::any('logout', 'Auth\LoginController@logout')->name('logout');
    Route::post('otp', 'Auth\LoginController@otp_login')->middleware('guest');

    Route::post('email/confirm', 'SettingsController@confirmEmailToken');

    Route::post('register', 'Auth\RegisterController@register')->middleware('captcha:register');
    Route::post('register/login/check', 'Auth\RegisterController@checkLogin');

    Route::prefix('password')->group(function (){
        Route::post('email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email')->middleware('captcha:recovery');
        Route::post('reset', 'Auth\ResetPasswordController@reset');
    });

    Route::prefix('vk')->group(function (){
        Route::get('redirect', 'Auth\LoginController@vkRedirect');
        Route::any('login', 'Auth\LoginController@vkLogin')->middleware('guest');
        Route::any('recovery', 'Auth\ForgotPasswordController@vkRecovery')->middleware('guest');

        Route::get('link', 'SettingsController@vkLink')->middleware('auth');
    });
});

Route::prefix('cases')->middleware('auth')->group(function (){
    Route::get('load', "CasesController@load");
    Route::post('open', "CasesController@open");
});

Route::prefix('profile')->middleware('auth')->group(function (){
    Route::get('load', "ProfileController@index");
    Route::get('kits', "ProfileController@loadKits");

    Route::post('kits/buy', "ProfileController@buyKit");

    Route::post('group/buy', "ProfileController@buygroup");
    Route::post('prefix/set', "ProfileController@prefix");
    Route::post('prefix/delete', "ProfileController@delprefix");

    Route::post('safetp', "ProfileController@stp")->middleware('captcha:stp');
    Route::post('kit/buy', "ProfileController@buykit");
    Route::post('unban/buy', "ProfileController@unban");

    Route::prefix('economy')->group(function (){
        Route::post('send', "ProfileController@sendMoney");
        Route::post('exchange', "ProfileController@exchange");
    });

    Route::prefix('punish')->group(function (){
        Route::get('/', "ProfileController@loadBans");
        Route::post('unban', "ProfileController@unban");
    });

    Route::prefix('oauth')->group(function (){
        Route::get('unlink/{string}', 'IntegrationController@unlink');
        Route::get('link/{string}', 'IntegrationController@linkURL');
    });
});

Route::prefix('settings')->middleware('auth')->group(function (){
    Route::get('/', "SettingsController@index");
    Route::post('save', "SettingsController@save");

    Route::post('profile', "SettingsController@profile");

    Route::post('vk/unlink', 'SettingsController@vkUnlink');
    Route::post('email/confirm', 'SettingsController@confirmEmail')->middleware('captcha:email_confirm');
    Route::post('email/bonus', 'SettingsController@takeBonus');

    Route::any('tg/link', 'SettingsController@tgLink');
});

Route::prefix('skins')->middleware('auth')->group(function (){
    Route::post('upload', "SkinController@upload")->middleware('auth');
    Route::post('delete', "SkinController@delete")->middleware('auth');
});

Route::prefix('moder')->middleware('auth')->group(function (){
    Route::get('load', "FeedbackController@moder")->middleware('auth');
    Route::post('send', "FeedbackController@moder_accept")->middleware('auth');
    Route::post('delete', "FeedbackController@moder_delete")->middleware('auth');
});

Route::prefix('ban')->middleware('auth')->group(function (){
    Route::get('list', "ProfileController@banlist");
});

Route::prefix('shop')->middleware('auth')->group(function (){
    Route::get('/', "ShopController@index");
    Route::post('load', "ShopController@load");
    Route::post('buy', "ShopController@buy");
});

Route::prefix('friends')->middleware('auth')->group(function (){
    Route::get('requests', "FriendsController@requests");

    Route::post('add', "FriendsController@add");
    Route::post('accept', "FriendsController@accept");
    Route::post('deny', "FriendsController@deny");
    Route::post('remove', "FriendsController@remove");
});

Route::prefix('news')->group(function (){
    Route::get('load', 'NewsController@index');
});
