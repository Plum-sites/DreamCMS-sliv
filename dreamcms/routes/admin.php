<?php

Route::group(['prefix' => 'admin', 'middleware' => ['web']], function () {
    Route::get('/', 'AdminController@layout');
    Route::get('/{any}', 'AdminController@layout')->where('any', '.*');
});

/*Route::group(['namespace' => 'Admin', 'prefix' => 'admin_old', 'middleware' => ['web', 'admin']], function () {
    //USER & PERMISSIONS
    CRUD::resource('user', 'UserCrudController');
    CRUD::resource('permission', 'PermissionCrudController');
    CRUD::resource('role', 'RoleCrudController');
    
    //SHOP & CART
    CRUD::resource('shops', 'ShopCrudController');
    CRUD::resource('shop_categories', 'ShopCategoryCrudController');
    CRUD::resource('shop_items', 'ShopItemsCrudController');
    CRUD::resource('cart_items', 'CartItemsCrudController');
    
    //Donate groups
    CRUD::resource('groups', 'DonateGroupsCrudController');
    CRUD::resource('offers', 'SpecialOffersCrudController');
    
    //Servers
    CRUD::resource('servers', 'ServersCrudController');
    
    //Articles
    CRUD::resource('article', 'ArticleCrudController');
    CRUD::resource('category', 'CategoryCrudController');
    CRUD::resource('tag', 'TagCrudController');
    
    //Shit
    Route::get('shop/export/{id}', 'ShopCrudController@export');
    
    Route::any('userlog', 'LogController@user');
    Route::any('adminlog', 'LogController@admin');
    
    Route::get('shopimport', 'ShopImportController@index');
    Route::post('shopimport', 'ShopImportController@csv');
    
    //Dashboard
    Route::prefix('dashboard')->group(function (){
        Route::get('/', 'DashboardController@dashboard');
        Route::post('server', 'DashboardController@server');
        Route::post('load_donaters', 'DashboardController@load_donaters');
        Route::post('chart', 'DashboardController@loadChart');
    });

    //Forum manager
    Route::prefix('fmanager')->namespace('Forum')->group(function () {
        Route::get('/', 'ForumManagerController@index');

        Route::prefix('user')->group(function (){
            Route::post('/', 'ForumManagerController@user');
            Route::get('find', 'ForumManagerController@user_find');

            Route::post('logs', 'ForumManagerController@userLogs');

            Route::prefix('sign')->group(function (){
                Route::post('remove', 'ForumManagerController@removeSign');
            });
        });

        Route::prefix('discussion')->group(function (){
            Route::post('/', 'ForumManagerController@discussion');

            Route::post('logs', 'ForumManagerController@discussionLogs');
        });

        Route::prefix('unban')->group(function (){
            Route::post('forum', 'ForumManagerController@unban_forum');
        });

        Route::prefix('ban')->group(function (){
            Route::post('forum', 'ForumManagerController@ban_forum');
        });
    });



    //User manager
    Route::prefix('manager')->group(function (){
        Route::get('/', 'ManagerController@index');
        
        Route::get('find_item', 'ManagerController@find_item');

        Route::post('login', 'ManagerController@login');

        Route::prefix('user')->group(function (){
            Route::post('/', 'ManagerController@user');
            Route::get('find', 'ManagerController@user_find');
            Route::post('pex', 'ManagerController@user_pexgroups');
            Route::post('balance', 'ManagerController@user_balance');
            Route::post('2fa', 'ManagerController@clear_2fa');
        });
    
        Route::prefix('donate')->group(function (){
            Route::any('give', 'ManagerController@donate_give');
            Route::post('remove', 'ManagerController@donate_remove');
        });

        Route::prefix('perms')->group(function (){
            Route::post('clear', 'ManagerController@clear_site_perms');
        });

        Route::prefix('pex')->group(function (){
            Route::post('clear', 'ManagerController@clear_server_perms');

            Route::prefix('group')->group(function (){
                Route::post('add', 'ManagerController@add_group');
                Route::post('remove', 'ManagerController@remove_group');
            });
        });
    
        Route::prefix('cart')->group(function (){
            Route::post('give', 'ManagerController@cart_give');
            Route::post('kit', 'ManagerController@kit_give');
            Route::post('remove', 'ManagerController@cart_remove');
        });
    
        Route::prefix('unban')->group(function (){
            Route::post('game', 'ManagerController@unban_game');
            Route::post('forum', 'ManagerController@unban_forum');
        });
    
        Route::prefix('ban')->group(function (){
            Route::post('game', 'ManagerController@ban_game');
            Route::post('forum', 'ManagerController@ban_forum');
        });
    });
    
    //Online statistics
    Route::prefix('online')->group(function (){
        Route::get('/', 'OnlineController@index');
        Route::post('chart', 'OnlineController@chart');
    });
    
    //Forum management
    Route::prefix('forum')->namespace('Forum')->group(function (){
        Route::get('/', 'ForumController@index');
        Route::get('logs', 'ForumController@logs');
        Route::get('permissions', 'ForumController@permissions');
        Route::get('moder', 'ForumController@moder');
        Route::get('activity', 'ForumController@activity');
        Route::get('chat', 'ForumController@chat');
    
        CRUD::resource('category', 'CategoryController');
    });
    
    //Moder entry
    Route::prefix('moder')->group(function (){
        Route::get('/', 'ModerEntryController@index');
        Route::post('search', 'ModerEntryController@search');
        Route::post('status', 'ModerEntryController@status');
        Route::post('comment', 'ModerEntryController@comment');
        CRUD::resource('/raw', 'ModerEntryCrudController');
    });
    
    Route::prefix('acl')->group(function (){
        Route::get('/', 'ExtendedPermissions@index');
        Route::post('/', 'ExtendedPermissions@select');
        Route::post('save', 'ExtendedPermissions@save');
    });
    
    //Server permission editor
    Route::prefix('permissions')->group(function (){
        Route::get('/', 'PermissionsController@index');
        Route::get('{id}', 'PermissionsController@server');
    
        Route::prefix('group')->group(function (){
            Route::post('create', 'PermissionsController@group_create');
            Route::post('delete', 'PermissionsController@group_delete');
    
            Route::post('list', 'PermissionsController@group_list');
            Route::post('info', 'PermissionsController@group_info');
            Route::post('players', 'PermissionsController@group_players');
            Route::post('clear', 'PermissionsController@group_clear');
    
    
            Route::post('default/set', 'PermissionsController@group_setdefault');
            Route::post('parent/set', 'PermissionsController@group_setparent');
            
            Route::post('perm/add', 'PermissionsController@group_addperm');
            Route::post('perm/remove', 'PermissionsController@group_removeperm');
    
            Route::post('prefix/set', 'PermissionsController@group_setprefix');
            Route::post('prefix/remove', 'PermissionsController@group_removeprefix');
    
            Route::post('suffix/set', 'PermissionsController@group_setsuffix');
            Route::post('suffix/remove', 'PermissionsController@group_removesuffix');
        });
    
        Route::prefix('user')->group(function (){
            Route::post('info', 'PermissionsController@user');
            Route::post('groups', 'PermissionsController@user_groups');
            Route::post('clear', 'PermissionsController@user_clear');
    
    
            Route::post('group/add', 'PermissionsController@user_addgroup');
            Route::post('group/remove', 'PermissionsController@user_removegroup');
    
            Route::post('perm/add', 'PermissionsController@user_addperm');
            Route::post('perm/remove', 'PermissionsController@user_removeperm');
    
    
            Route::post('prefix/set', 'PermissionsController@user_setprefix');
            Route::post('prefix/remove', 'PermissionsController@user_removeprefix');
    
            Route::post('suffix/set', 'PermissionsController@user_setsuffix');
            Route::post('suffix/remove', 'PermissionsController@user_removesuffix');
    
        });
    });
    
    //RCON
    Route::prefix('rcon')->group(function (){
        Route::get('/', 'RconController@index');
        Route::post('send', 'RconController@cmd');
    });
});*/