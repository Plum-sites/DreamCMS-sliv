<?php

namespace App\Http\Controllers\API\Admin;

use App\Achievements\Achievement;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\CartItem;
use App\Models\CaseChest;
use App\Models\DonateGroup;
use App\Models\Forum\Category;
use App\Models\Forum\Post;
use App\Models\Kit;
use App\Models\ModerEntry;
use App\Models\Server;
use App\Models\Shop;
use App\Models\ShopCategory;
use App\Models\ShopItem;
use App\Models\SpecialOffer;
use App\Models\Tag;
use App\Models\User;
use Backpack\PageManager\app\Models\Page;
use Backpack\PermissionManager\app\Models\Permission;
use Backpack\PermissionManager\app\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class ExtendedPermissions extends Controller
{
    const DEFAULT_MAP = [
        'view' => 'Просматривать',
        'create' => 'Создавать',
        'edit' => 'Обновлять',
        'delete' => 'Удалять'
    ];

    public static $COMMON_MAP = [
        'Форум' => [
            'forum' => [
                'category' => 'Форум',
                'permissions' => [
                    'access' => 'Доступ',
                    'edit' => 'Изменять',
                    'move' => 'Перемещать',
                    'delete' => 'Удалить',
                ],
                'child' => [
                    'discussion' => [
                        'label' => 'Темы',
                        'permissions' => ['access', 'move', 'delete']
                    ],
                    'post' => [
                        'label' => 'Посты',
                        'permissions' => ['edit', 'delete']
                    ],
                    'template' => [
                        'label' => 'Использование шаблонов',
                        'permissions' => ['access']
                    ],
                ]
            ],
        ],
        'Админ-панель' => [
            'admin' => [
                'category' => 'Админ-панель',
                'permissions' => [
                    'access' => 'Доступ'
                ],
                'child' => [
                    'access' => [
                        'label' => 'Доступ к админ-панели',
                        'permissions' => ['access']
                    ],
                    'dashboard' => [
                        'label' => 'Доступ к главной панели',
                        'permissions' => ['access']
                    ],
                    'dashboard_profit' => [
                        'label' => 'Доступ к панели дохода',
                        'permissions' => ['access']
                    ],
                    'refer' => [
                        'label' => 'Доступ к панели реферов',
                        'permissions' => ['access']
                    ],
                    'manager' => [
                        'label' => 'Доступ к быстрому менеджеру',
                        'permissions' => ['access']
                    ],
                    'online' => [
                        'label' => 'Доступ к трекеру онлайна',
                        'permissions' => ['access']
                    ],
                    'logs' => [
                        'label' => 'Доступ к логам',
                        'permissions' => ['access']
                    ],
                    'acl' => [
                        'label' => 'Доступ к ACL',
                        'permissions' => ['access']
                    ],
                    'forum_manager' => [
                        'label' => 'Доступ к форумному менеджеру',
                        'permissions' => ['access']
                    ],
                    'moder_entry' => [
                        'label' => 'Доступ к заявкам модераторов',
                        'permissions' => ['access']
                    ],
                    'rcon' => [
                        'label' => 'Доступ к RCON',
                        'permissions' => ['access']
                    ]
                ],
            ],

            //Dashboard

            'dashboard' => [
                'category' => 'Главная панель',
                'permissions' => [
                    'view' => 'Просматривать'
                ],
                'child' => [
                    'stat' => [
                        'label' => 'Верхние карточки статистики',
                        'permissions' => ['view']
                    ],
                    'password_chart' => [
                        'label' => 'График смен пароля',
                        'permissions' => ['view']
                    ],
                    'bans_chart' => [
                        'label' => 'График банов и причин',
                        'permissions' => ['view']
                    ],
                    '2fa_chart' => [
                        'label' => 'График 2FA',
                        'permissions' => ['view']
                    ],
                    'group_buys' => [
                        'label' => 'График покупок групп',
                        'permissions' => ['view']
                    ],
                    'shop_buys' => [
                        'label' => 'График покупок предметов',
                        'permissions' => ['view']
                    ],
                    'api_buys' => [
                        'label' => 'График API списаний',
                        'permissions' => ['view']
                    ]
                ],
            ],

            'dashboard_profit' => [
                'category' => 'Панель дохода',
                'permissions' => [
                    'view' => 'Просматривать'
                ],
                'child' => [
                    'monthly_chart' => [
                        'label' => 'Месячный доход за все время',
                        'permissions' => ['view']
                    ],
                    'daily_chart' => [
                        'label' => 'Дневной доход',
                        'permissions' => ['view']
                    ],
                    'systems_chart' => [
                        'label' => 'Платежные системы',
                        'permissions' => ['view']
                    ],
                    'daily_groups' => [
                        'label' => 'Донат-группы',
                        'permissions' => ['view']
                    ],
                    'active_groups' => [
                        'label' => 'Активные донат-группы',
                        'permissions' => ['view']
                    ],
                    'server_profit' => [
                        'label' => 'График дохода по серверам',
                        'permissions' => ['view']
                    ],
                    'branch_profit' => [
                        'label' => 'График дохода по веткам',
                        'permissions' => ['view']
                    ],
                    'top_buys' => [
                        'label' => 'Топ покупаемых предметов',
                        'permissions' => ['view']
                    ],
                ]
            ],

            'refer' => [
                'category' => 'Панель реферов',
                'permissions' => [
                    'view' => 'Просматривать'
                ],
                'child' => [
                    'other' => [
                        'label' => 'Чужая статистика',
                        'permissions' => ['view']
                    ]
                ],
            ],

            'manager' => [
                'category' => 'Быстрый менеджер',
                'permissions' => [
                    'give' => 'Выдавать',
                    'revoke' => 'Забирать',
                    'edit' => 'Обновлять',
                    'delete' => 'Удалять',
                ],
                'child' => [
                    'balance_real' => [
                        'label' => 'Баланс рублей',
                        'permissions' => ['give', 'revoke']
                    ],
                    'balance_money' => [
                        'label' => 'Баланс монет',
                        'permissions' => ['give', 'revoke']
                    ],
                    'reputation' => [
                        'label' => 'Репутация',
                        'permissions' => ['give', 'revoke']
                    ],
                    '2fa' => [
                        'label' => '2FA',
                        'permissions' => ['delete']
                    ],

                    'donate' => [
                        'label' => 'Донат-группы',
                        'permissions' => ['give', 'revoke']
                    ],
                    'cart' => [
                        'label' => 'Корзина',
                        'permissions' => ['give', 'revoke']
                    ],
                    'cart_kit' => [
                        'label' => 'Корзина (киты)',
                        'permissions' => ['give']
                    ],
                    'permissions' => [
                        'label' => 'Права на серверах',
                        'permissions' => ['give', 'revoke']
                    ],
                    'ban_game' => [
                        'label' => 'Бан в игре',
                        'permissions' => ['give', 'delete']
                    ],
                    'ban_site' => [
                        'label' => 'Бан на форуме',
                        'permissions' => ['give', 'delete']
                    ],
                ],
            ],

            'elfinder' => [
                'category' => 'Файловый менеджер',
                'permissions' => [
                    'create' => 'Создавать',
                    'edit' => 'Обновлять',
                    'delete' => 'Удалять',
                ],
                'child' => [
                    'files' => [
                        'label' => 'Файлы',
                        'permissions' => ['create', 'edit', 'delete']
                    ],
                ],
            ],

            'forum_manager' => [
                'category' => 'Форумный менеджер',
                'permissions' => [
                    'access' => 'Доступ',
                    'edit' => 'Изменять',
                    'delete' => 'Удалить',
                ],
                'child' => [
                    'logs' => [
                        'label' => 'Доступ к логам',
                        'permissions' => ['access']
                    ],
                    'ban' => [
                        'label' => 'Выдача блокировок игрока',
                        'permissions' => ['access']
                    ],
                    'sign' => [
                        'label' => 'Подпись игрока',
                        'permissions' => ['delete']
                    ]
                ],
            ],

            'moder_entry' => [
                'category' => 'Заявки в модераторы',
                'permissions' => [
                    'edit' => 'Изменять'
                ],
                'child' => [
                    'entry' => [
                        'label' => 'Заявки',
                        'permissions' => ['edit']
                    ],
                ],
            ]
        ],

        'Ресурсы админ-панели' => [
            Article::class => [
                'category' => 'Новости',
                'permissions' => self::DEFAULT_MAP
            ],

            Tag::class => [
                'category' => 'Теги новостей',
                'permissions' => self::DEFAULT_MAP
            ],

            \App\Models\Category::class => [
                'category' => 'Категории новостей',
                'permissions' => self::DEFAULT_MAP
            ],

            User::class => [
                'category' => 'Пользователи',
                'permissions' => self::DEFAULT_MAP
            ],

            Category::class => [
                'category' => 'Категории форума',
                'permissions' => self::DEFAULT_MAP
            ],

            CartItem::class => [
                'category' => 'Корзина игрока',
                'permissions' => self::DEFAULT_MAP
            ],

            DonateGroup::class => [
                'category' => 'Донат группы',
                'permissions' => self::DEFAULT_MAP
            ],

            Post::class => [
                'category' => 'Сообщения форума',
                'permissions' => self::DEFAULT_MAP
            ],

            ModerEntry::class => [
                'category' => 'Заявки на модератора',
                'permissions' => self::DEFAULT_MAP
            ],

            Page::class => [
                'category' => 'Статические страницы',
                'permissions' => self::DEFAULT_MAP
            ],

            Server::class => [
                'category' => 'Сервера',
                'permissions' => self::DEFAULT_MAP
            ],

            Shop::class => [
                'category' => 'Магазины блоков',
                'permissions' => self::DEFAULT_MAP
            ],

            ShopCategory::class => [
                'category' => 'Категории магазина блоков',
                'permissions' => self::DEFAULT_MAP
            ],

            ShopItem::class => [
                'category' => 'Предметы магазина блоков',
                'permissions' => self::DEFAULT_MAP
            ],

            SpecialOffer::class => [
                'category' => 'Специальные предложения',
                'permissions' => self::DEFAULT_MAP
            ],

            Kit::class => [
                'category' => 'Киты',
                'permissions' => self::DEFAULT_MAP
            ],

            Achievement::class => [
                'category' => 'Ачивки',
                'permissions' => self::DEFAULT_MAP
            ],

            CaseChest::class => [
                'category' => 'Кейсы',
                'permissions' => self::DEFAULT_MAP
            ],
        ],

        'Личный кабинет' => [
            'prefix' => [
                'category' => 'Префикс',
                'permissions' => [
                    'edit' => 'Обновлять',
                    'delete' => 'Удалять'
                ],
                'child' => [
                    'text' => [
                        'label' => 'Текст',
                        'permissions' => ['edit', 'delete']
                    ],
                    'prefix_color' => [
                        'label' => 'Цвет префикса',
                        'permissions' => ['edit', 'delete']
                    ],
                    'nick_color' => [
                        'label' => 'Цвет ника',
                        'permissions' => ['edit', 'delete']
                    ],
                    'msg_color' => [
                        'label' => 'Цвет сообщения',
                        'permissions' => ['edit', 'delete']
                    ]
                ],
            ],

            'money' => [
                'category' => 'Баланс',
                'permissions' => [
                    'send' => 'Передавать',
                    'recieve' => 'Получать'
                ],
                'child' => [
                    'realmoney' => [
                        'label' => 'Рубли',
                        'permissions' => ['send', 'recieve']
                    ],
                    'money' => [
                        'label' => 'Монеты',
                        'permissions' => ['send', 'recieve']
                    ]
                ],
            ],

            'upload' => [
                'category' => 'Загрузка скинов и плащей',
                'permissions' => [
                    'edit' => 'Обновлять',
                    'delete' => 'Удалять'
                ],
                'child' => [
                    'skin' => [
                        'label' => 'Обычный скин',
                        'permissions' => ['edit', 'delete']
                    ],
                    'skin.hd' => [
                        'label' => 'HD скин',
                        'permissions' => ['edit', 'delete']
                    ],
                    'cloak' => [
                        'label' => 'Обычный плащ',
                        'permissions' => ['edit', 'delete']
                    ],
                    'cloak.hd' => [
                        'label' => 'HD плащ',
                        'permissions' => ['edit', 'delete']
                    ],
                ],
            ],
        ],
    ];

    public static $MODEL_MAP = [
        Category::class => [
            'category' => 'Форумные разделы',
            'label' => 'slug',
            'method' => 'get',
            'permissions' => [
                'view' => 'Просматривать',
                'pin_thread' => 'Закреплять темы',
                'create_thread' => 'Создавать темы',
                'close_thread' => 'Закрывать темы',
                'hide_thread' => 'Скрывать темы',
                'delete_thread' => 'Удалять темы',
                'move_thread' => 'Переносить темы'
            ]
        ],

        //Manager
        DonateGroup::class => [
            'category' => 'Донат группы игроков',
            'label' => 'pexname',
            'method' => 'get',
            'permissions' => [
                'give' => 'Выдавать',
                'revoke' => 'Снимать'
            ]
        ],

        //Rcon
        Server::class => [
            'category' => 'Управление серверами',
            'label' => 'name',
            'method' => 'get',
            'permissions' => [
                'rcon' => 'RCON',
                'broadcast' => 'Объявления',
                'restart' => 'Перезапускать',
                'hold' => 'Замораживать',
                'unhold' => 'Размораживать'
            ]
        ]
    ];

    public function loadPermissions(Request $request)
    {
        abort_if(!\Auth::user()->hasRole('admin'), 403, "Только администраторы имеют доступ!");

        $user = $request->get('user') ? User::find($request->get('user')) : null;
        $role = $request->get('role') ? Role::find($request->get('role')) : null;

        if ($user || $role) {
            $permissible = $user ? User::class : Role::class;
            $permissible_id = $user ? $user->id : ($role ? $role->id : 0);

            $perms = $user ? $user->permissions : $role->permissions;
            $perms_names = [];

            foreach ($perms as $perm) {
                $perms_names[] = $perm->name;
            }

            $models_perms = \DB::table('ext_permissions')->where([
                'permissible' => $permissible,
                'permissible_id' => $permissible_id,
            ])->get();

            $mperms = collect();

            $models_perms->each(function ($perm) use (&$mperms) {
                $current = $mperms->get($perm->model, []);
                $current[$perm->model_id ? $perm->model_id : "global"] = json_decode($perm->perms);

                $mperms->put($perm->model, $current);
            });

            $models = collect(self::$MODEL_MAP)->map(function ($item, $key) {
                $item['entity'] = $key::{$item['method']}();
                return $item;
            });

            return \Response::json([
                'acluser' => $user,
                'aclrole' => $role,
                'common_map' => self::$COMMON_MAP,
                'models_map' => $models,
                'models_perms' => $mperms,
                'perms' => $perms_names,
            ]);
        } else {
            return \Response::json([
                'roles' => Role::all(),
            ]);
        }
    }

    public static function checkModelAccess($user, $class, $perm, $id = false)
    {
        /* @var User $user */
        if ($user) {
            if ($id) {
                $q = \DB::selectOne('SELECT JSON_SEARCH(perms, \'one\', ?) IS NOT NULL as `has` FROM `ext_permissions` WHERE `permissible` = ?  AND `permissible_id` = ? AND `model` = ? AND `model_id` = ?', [
                    $perm,
                    User::class,
                    $user->id,
                    $class,
                    $id
                ]);

                if ($q && $q->has) return true;
            } else {
                $q = \DB::selectOne('SELECT JSON_SEARCH(perms, \'one\', ?) IS NOT NULL as `has` FROM `ext_permissions` WHERE `permissible` = ?  AND `permissible_id` = ? AND `model` = ? AND `model_id` IS NULL', [
                    $perm,
                    User::class,
                    $user->id,
                    $class
                ]);

                if ($q && $q->has) return true;
            }
        }

        //Check all user roles

        if ($user){
            $roles = $user->getAllRoles();
        }else{
            $roles = [Role::findByName('member', 'web')];
        }

        foreach ($roles as $role) {
            if ($id){
                $q = \DB::selectOne('SELECT JSON_SEARCH(perms, \'one\', ?) IS NOT NULL as `has` FROM `ext_permissions` WHERE `permissible` = ?  AND `permissible_id` = ? AND `model` = ? AND `model_id` = ?', [
                    $perm,
                    Role::class,
                    $role->id,
                    $class,
                    $id
                ]);
            } else{
                $q = \DB::selectOne('SELECT JSON_SEARCH(perms, \'one\', ?) IS NOT NULL as `has` FROM `ext_permissions` WHERE `permissible` = ?  AND `permissible_id` = ? AND `model` = ? AND `model_id` IS NULL', [
                    $perm,
                    Role::class,
                    $role->id,
                    $class
                ]);
            }

            if ($q && $q->has) return true;
        }

        return false;
    }

    public function setPermissions(Request $request)
    {
        abort_if(!\Auth::user()->hasRole('admin'), 403, "Только администраторы имеют доступ!");

        /* @var User $user */
        $user = $request->get('user') ? User::find($request->get('user')) : null;

        /* @var Role $role */
        $role = $request->get('role') ? Role::find($request->get('role')) : null;

        $arr = $request->get('arr');

        $sync_permissions = collect();
        $model_permissions = collect();
        $modelid_permissions = collect();

        if (!$user && !$role) {
            return \Response::json([
                'error' => true,
                'message' => 'Выберите роль или пользователя!'
            ]);
        }

        foreach ($arr as $permission) {
            if ($permission['type'] == 'perm') {
                $perm_info = explode('.', $permission['perm']);
                $path = $perm_info[0] . 'child' . $perm_info[1];
                if ($perm = Arr::get(self::$COMMON_MAP, $path)) {
                    if (isset($perm['super_admin']) && $perm['super_admin']) {
                        if (!\Auth::user()->isSuperAdmin()) {
                            continue;
                        }
                    }
                }

                $backperm = Permission::findOrCreate($permission['perm'], 'web');
                $sync_permissions->push($backperm);
            }
            if ($permission['type'] == 'bymodel') {
                //$permission['model'] = str_replace('_', '\\', $permission['model']);

                $perms = $model_permissions->get($permission['model'], []);
                $perms[] = $permission['perm'];
                $model_permissions->put($permission['model'], $perms);
            }
            if ($permission['type'] == 'byid') {
                //$permission['model'] = str_replace('_', '\\', $permission['model']);

                /* @var Collection $ids */
                $ids = $modelid_permissions->get($permission['model'], collect());

                $perms_for_id = $ids->get($permission['id'], []);
                $perms_for_id[] = $permission['perm'];

                $ids->put($permission['id'], $perms_for_id);

                $modelid_permissions->put($permission['model'], $ids);
            }
        }

        $permissible = $user ? User::class : Role::class;
        $permissible_id = $user ? $user->id : ($role ? $role->id : 0);

        \DB::table('ext_permissions')->where([
            'permissible' => $permissible,
            'permissible_id' => $permissible_id,
        ])->delete();

        foreach ($model_permissions as $model => $perms) {
            \DB::table('ext_permissions')->insert([
                'permissible' => $permissible,
                'permissible_id' => $permissible_id,
                'model' => $model,
                'perms' => json_encode($perms)
            ]);
        }

        foreach ($modelid_permissions as $model => $ids) {
            foreach ($ids as $id => $perms) {
                \DB::table('ext_permissions')->insert([
                    'permissible' => $permissible,
                    'permissible_id' => $permissible_id,
                    'model' => $model,
                    'model_id' => $id,
                    'perms' => json_encode($perms)
                ]);
            }
        }

        if ($user) $user->syncPermissions($sync_permissions);
        if ($role) $role->syncPermissions($sync_permissions);

        return \Response::json([
            'success' => true,
            'message' => 'Вы успешно обновили права!'
        ]);
    }
}