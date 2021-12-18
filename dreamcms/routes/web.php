<?php
Route::get('/payments/unitpay', "PaymentController@unitpay");
Route::post('/payments/skinpay', "PaymentController@skinpaySuccess");
Route::post('/payments/enot', "PaymentController@enotSuccess");
Route::any('/payments/digiseller', "PaymentController@digiseller");

Route::any('/payments/obmenka/{any}', "PaymentController@obmenkaSuccess");

Route::any('/webhook/telegram/q35BdeStfqNZdn2iJPoMOFwp', "WebController@telegram");

Route::get('/skin/{any}/{string?}/{size?}', "SkinController@skin");

Route::get('/game/lk', "GameController@index");

Route::fallback('WebController@layout');