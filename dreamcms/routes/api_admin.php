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

Route::group([
    'prefix'     => 'admin',
    'middleware' => ['web', 'auth:api'],
    'namespace'  => 'Admin',
], function () {
    Route::prefix('core')->group(function (){
        Route::post('load', 'CoreController@loadInfo');
    });

    Route::prefix('crud')->namespace('CRUD')->group(function (){
        Route::crud('user', 'UserCrudController');
        Route::crud('permission', 'PermissionCrudController');
        Route::crud('role', 'RoleCrudController');

        //SHOP & CART
        Route::crud('shops', 'ShopCrudController');
        Route::crud('shop_categories', 'ShopCategoryCrudController');
        Route::crud('shop_items', 'ShopItemsCrudController');
        Route::crud('cart_items', 'CartItemsCrudController');

        //Donate groups
        Route::crud('groups', 'DonateGroupsCrudController');
        Route::crud('offers', 'SpecialOffersCrudController');

        //Servers
        Route::crud('servers', 'ServersCrudController');

        //Articles
        Route::crud('article', 'ArticleCrudController');
        Route::crud('category', 'CategoryCrudController');
        Route::crud('tag', 'TagCrudController');

        //Page
        Route::crud('page', 'PageCrudController');

        //Forum
        Route::crud('forum_category', 'ForumCategoryCrudController');

        //Settings
        Route::crud('settings', 'SettingCrudController');

        //Kits
        Route::crud('kits', 'KitsCrudController');

        //Achievement
        Route::crud('achievement', 'AchievementCrudController');

        Route::crud('cases', 'CasesCrudController');
    });

    // DASHBOARD

    Route::prefix('dashboard')->group(function (){
        Route::post('load', 'DashboardController@loadInfo');
        Route::post('chart', 'DashboardController@loadChart');
        Route::post('posts', 'DashboardController@lastPosts');

        Route::prefix('profit')->group(function (){
            Route::post('server', 'DashboardController@serverProfit');
            Route::post('stats', 'DashboardController@loadStats');
            Route::post('top_buys', 'DashboardController@topBuys');

            Route::post('chart', 'DashboardController@loadChart');
        });
    });

    Route::prefix('refer')->group(function (){
        Route::post('chart', 'ReferController@loadChart');
    });

    // LOGS

    Route::prefix('logs')->group(function (){
        Route::post('user', 'LogController@userLogs');
        Route::post('admin', 'LogController@adminLogs');
    });

    // FORUM

    Route::prefix('forum')->group(function () {
        Route::prefix('manager')->group(function (){
            Route::prefix('user')->group(function (){
                Route::post('/', 'ForumManagerController@loadUser');
                Route::get('find', 'ForumManagerController@findUser');

                Route::post('logs', 'ForumManagerController@userLogs');

                Route::prefix('sign')->group(function (){
                    Route::post('remove', 'ForumManagerController@removeSign');
                });
            });

            Route::prefix('discussion')->group(function (){
                Route::post('/', 'ForumManagerController@findDiscussion');

                Route::post('logs', 'ForumManagerController@discussionLogs');
            });

            Route::prefix('unban')->group(function (){
                Route::post('forum', 'ForumManagerController@unbanForum');
            });

            Route::prefix('ban')->group(function (){
                Route::post('forum', 'ForumManagerController@banForum');
            });
        });
    });

    // USER MANAGER

    Route::prefix('manager')->group(function (){
        Route::get('item/find', 'ManagerController@findItem');

        Route::post('login', 'ManagerController@loginAs');

        Route::prefix('user')->group(function (){
            Route::post('/', 'ManagerController@loadUser');
            Route::get('find', 'ManagerController@findUser');
            Route::post('pex', 'ManagerController@loadUserServerGroups');
            Route::post('balance', 'ManagerController@updateBalance');
            Route::post('2fa', 'ManagerController@remove2FA');
        });

        Route::prefix('donate')->group(function (){
            Route::any('give', 'ManagerController@giveDonateGroup');
            Route::post('remove', 'ManagerController@removeDonateGroup');
        });

        Route::prefix('perms')->group(function (){
            Route::post('clear', 'ManagerController@clearSitePerms');
        });

        Route::prefix('pex')->group(function (){
            Route::post('clear', 'ManagerController@clearServerPerms');

            Route::prefix('group')->group(function (){
                Route::post('add', 'ManagerController@giveServerGroup');
                Route::post('remove', 'ManagerController@removeServerGroup');
            });
        });

        Route::prefix('cart')->group(function (){
            Route::post('give', 'ManagerController@giveCartItem');
            Route::post('kit', 'ManagerController@giveKit');
            Route::post('remove', 'ManagerController@removeCartItem');
        });

        Route::prefix('ban')->group(function (){
            Route::post('game', 'ManagerController@gameBan');
            Route::post('forum', 'ManagerController@forumBan');
        });

        Route::prefix('unban')->group(function (){
            Route::post('game', 'ManagerController@gameUnban');
            Route::post('forum', 'ManagerController@forumUnban');
        });
    });

    // ONLINE CHART

    Route::prefix('online')->group(function (){
        Route::post('servers', 'OnlineController@loadServers');
        Route::post('chart', 'OnlineController@loadChart');
    });

    // MODER ENTRY

    Route::prefix('moder')->group(function (){
        Route::post('search', 'ModerEntryController@search');
        Route::post('status', 'ModerEntryController@updateStatus');
        Route::post('comment', 'ModerEntryController@updateComment');
        Route::post('logs', 'ModerEntryController@logs');
        //CRUD::resource('/raw', 'ModerEntryCrudController');
    });

    // ACL

    Route::prefix('acl')->group(function (){
        Route::post('load', 'ExtendedPermissions@loadPermissions');
        Route::post('save', 'ExtendedPermissions@setPermissions');
    });

    // RCON

    Route::prefix('rcon')->group(function (){
        Route::post('send', 'RconController@sendCommand');
    });
});

//Route::prefix('admin')->middleware('auth:api')->namespace('Admin')->group(function (){
//
//});
//
