<?php

/*
|--------------------------------------------------------------------------
| Backpack\Settings Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are
| handled by the Backpack\Settings package.
|
*/

Route::group([
    'namespace'  => 'App\Http\Controllers\API\Admin\CRUD',
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => ['web', 'admin-access'],
], function () {
    Route::crud('setting', 'SettingCrudController');
});
