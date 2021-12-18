// Sidebar Routers
export const menus = {
	'Главное': [
		{
			action: 'zmdi-view-dashboard',
			title: 'Статистика',
			items: [
				{ title: 'Главная панель', path: '/admin/dashboard', exact: true},
				{ title: 'Панель дохода', path: '/admin/dashboard/profit', exact: true},
				{ title: 'Реферальная система', path: '/admin/refer', exact: true},
			]
		},
		{
			action: 'zmdi-accounts-list-alt',
			title: 'Менеждер игроков',
			path: '/admin/manager',
			exact: true
		},
		{
			action: 'zmdi-timer',
			title: 'Трекер онлайна',
			path: '/admin/online',
			exact: true
		},
		{
			action: 'zmdi-search',
			title: 'Логи пользователей',
			path: '/admin/logs',
			exact: true
		},
		{
			action: 'zmdi-rss',
			title: 'Новости',
			items: [
				{ title: 'Статьи', path: '/admin/crud/article', exact: true},
				{ title: 'Категории', path: '/admin/crud/category', exact: true},
				{ title: 'Теги', path: '/admin/crud/tag', exact: true},
			]
		},
		{
			action: 'zmdi-accounts',
			title: 'Пользователи',
			items: [
				{ title: 'Редактирование', path: '/admin/crud/user', exact: true},
			]
		},
		{
			action: 'zmdi-traffic',
			title: 'Управление правами',
			items: [
				{ title: 'Группы сайта', path: '/admin/crud/role', exact: true},
				{ title: 'Права сайта', path: '/admin/crud/permission', exact: true},
				{ title: 'ACL', path: '/admin/acl', exact: true},
			]
		},
		{
			action: 'zmdi-collection-text',
			title: 'Статические страницы',
			path: '/admin/crud/page',
			exact: true
		},
		{
			action: 'zmdi-comments',
			title: 'Форум',
			items: [
				{ title: 'Категории', path: '/admin/crud/forum_category', exact: true},
				{ title: 'Менеджер', path: '/admin/forum/manager', exact: true},
			]
		},
		{
			action: 'zmdi-comment-outline',
			title: 'Обратная связь',
			items: [
				{ title: 'Заявки модераторов', path: '/admin/moder', exact: true},
			]
		},
		{
			action: 'zmdi-shopping-cart',
			title: 'Магазин блоков',
			items: [
				{ title: 'Магазины', path: '/admin/crud/shops', exact: true},
				{ title: 'Категории', path: '/admin/crud/shop_categories', exact: true},
				{ title: 'Предметы и товары', path: '/admin/crud/shop_items', exact: true},
				{ title: 'Корзина игроков', path: '/admin/crud/cart_items', exact: true},
			]
		},
		{
			action: 'zmdi-money-box',
			title: 'Донат',
			items: [
				{ title: 'Группы', path: '/admin/crud/groups', exact: true},
				{ title: 'Киты', path: '/admin/crud/kits', exact: true},
				{ title: 'Спец. предложения', path: '/admin/crud/offers', exact: true},
				{ title: 'Предметы и товары', path: '/admin/crud/shop_items', exact: true},
				{ title: 'Корзина игроков', path: '/admin/crud/cart_items', exact: true},
			]
		},
		{
			action: 'zmdi-view-list-alt',
			title: 'Сервера',
			items: [
				{ title: 'Редактирование серверов', path: '/admin/crud/servers', exact: true},
				{ title: 'RCON', path: '/admin/servers/rcon', exact: true},
			]
		}
	],
}
