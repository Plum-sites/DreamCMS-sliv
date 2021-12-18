export default [
  {
    header: 'Статистика'
  },
  {
    title: 'Панели',
    icon: 'HomeIcon',
    children: [
      {
        title: 'Главная панель',
        route: 'dashboard',
      },
      {
        title: 'Панель дохода',
        route: 'dashboard_profit',
      },
      {
        title: 'Реферальная система',
        route: 'refer',
      }
    ],
  },

  {
    header: 'Пользователи'
  },
  {
    title: 'Поиск',
    icon: 'SearchIcon',
    route: {name: 'crud', params: {model: 'user'}},
  },

  {
    title: 'Менеджер игроков',
    icon: 'UserIcon',
    route: 'manager',
  },

  {
    title: 'Трекер онлайна',
    icon: 'CheckSquareIcon',
    route: 'online',
  },

  {
    title: 'Логи игроков',
    icon: 'ListIcon',
    route: 'userlogs',
  },

  {
    header: 'Контент'
  },
  {
    title: 'Новости',
    icon: 'PackageIcon',
    children: [
      {
        title: 'Статьи',
        route: {name: 'crud', params: {model: 'article'}},
      },
      {
        title: 'Категории',
        route: {name: 'crud', params: {model: 'category'}},
      },
      {
        title: 'Теги',
        route: {name: 'crud', params: {model: 'tag'}},
      }
    ],
  },
  {
    title: 'Страницы',
    route: {name: 'crud', params: {model: 'page'}},
    icon: 'FileIcon',
  },

  {
    header: 'Управление'
  },
  {
    title: 'ACL',
    route: 'acl',
    icon: 'UserCheckIcon',
  },
  {
    title: 'Права',
    icon: 'UserCheckIcon',
    children: [
      {
        title: 'Группы сайта',
        route: {name: 'crud', params: {model: 'role'}},
        icon: 'UsersIcon',
      },
      {
        title: 'Права сайта',
        route: {name: 'crud', params: {model: 'permission'}},
        icon: 'AwardIcon'
      }
    ],
  },

  {
    header: 'Форум и связь'
  },
  {
    title: 'Менеджер',
    route: 'forum_manager',
    icon: 'UserMinusIcon',
  },
  {
    title: 'Форум',
    icon: 'SendIcon',
    children: [
      {
        title: 'Категории',
        route: {name: 'crud', params: {model: 'forum_category'}},
        icon: 'ListIcon',
      }
    ],
  },
  {
    title: 'Заявки модераторов',
    route: 'moder_entry',
    icon: 'UserPlusIcon'
  },

  {
    header: 'Донат'
  },
  {
    title: 'Магазин блоков',
    icon: 'ShoppingBagIcon',
    children: [
      {
        title: 'Магазины',
        route: {name: 'crud', params: {model: 'shops'}}
      },
      {
        title: 'Категории',
        route: {name: 'crud', params: {model: 'shop_categories'}}
      },
      {
        title: 'Товары',
        route: {name: 'crud', params: {model: 'shop_items'}}
      },
      {
        title: 'Корзина игроков',
        route: {name: 'crud', params: {model: 'cart_items'}}
      }
    ],
  },

  {
    title: 'Группы',
    route: {name: 'crud', params: {model: 'groups'}},
    icon: 'UsersIcon'
  },
  {
    title: 'Киты',
    route: {name: 'crud', params: {model: 'kits'}},
    icon: 'BoxIcon',
  },
  {
    title: 'Кейсы',
    route: {name: 'crud', params: {model: 'cases'}},
    icon: 'PackageIcon',
  },
  {
    title: 'Спец. предложения',
    route: {name: 'crud', params: {model: 'offers'}},
    icon: 'GiftIcon',
  },

  {
    header: 'Сервера'
  },
  {
    title: 'RCON',
    route: 'rcon',
    icon: 'SendIcon'
  },
  {
    title: 'Настройка',
    route: {name: 'crud', params: {model: 'settings'}},
    icon: 'ServerIcon',
  }
]
