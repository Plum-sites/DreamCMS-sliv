const ErrorNotFound = () => import("../pages/ErrorNotFound");

const ForumContainer = () => import("../ForumContainer");
const WideContainer = () => import("../WideContainer");
const Container = () => import('../Container');

const News = () => import('../pages/News');
const StaticPage = () => import("../pages/StaticPage");
const Shop = () => import("../pages/Shop");

const VKAuth = () => import("../pages/VKAuth");
const OAuth = () => import("../pages/OAuth");
const EmailConfirm = () => import("../pages/EmailConfirm");
const AuthRecovery = () => import("../pages/AuthRecovery");

const UserProfile = () => import("../pages/UserProfile");

const Cabinet = () => import("../pages/Cabinet");
const CabinetIndex = () => import("../pages/CabinetIndex");
const CabinetDonate = () => import("../pages/CabinetDonate");
const CabinetKits = () => import("../pages/CabinetKits");
const CabinetProfile = () => import("../pages/CabinetProfile");
const CabinetSecurity = () => import("../pages/CabinetSecurity");
const CabinetPunish = () => import("../pages/CabinetPunish");

const Banlist = () => import("../pages/Banlist");

const Pay = () => import("../pages/Pay");

const ModerEntry = () => import("../pages/ModerEntry");

const Forum = () => import("../pages/Forum");
const ForumCategory = () => import("../pages/ForumCategory");
const ForumCreate = () => import('../pages/ForumCreate');
const ForumDiscussion = () => import("../pages/ForumDiscussion");
const ForumAdmins = () => import("../pages/ForumAdmins");
const ForumSearch = () => import("../pages/ForumSearch");

const Notifications = () => import("../pages/Notifications");


export default [
   {
      path: '/',
      component: Container,
      children: [
         {
            path: '/',
            component: News,
            name: 'news',
            meta: {
               title: 'DreamCMS | Новости',
               metaTags: [
                  {
                     name: 'description',
                     content: 'Самые последние новости о проекте'
                  },
                  {
                     property: 'og:description',
                     content: 'Самые последние новости о проекте'
                  }
               ]
            }
         },
         {
            path: '/notifications',
            component: Notifications,
            name: 'notifications',
            meta: {
               auth: true,
               title: 'Уведомления'
            },
         },
         {
            path: '/pay',
            component: Pay,
            name: 'pay'
         },
         {
            path: '/page/:name',
            component: StaticPage,
            name: 'page'
         },
         {
            path: '/banlist',
            component: Banlist,
            name: 'banlist',
            meta: {
               auth: true,
               title: 'Банлист'
            },
         },
         {
            path: '/moder/entry',
            component: ModerEntry,
            name: 'moderentry',
            meta: {
               auth: true,
               title: 'Заявка в модераторы'
            },
         },
         {
            path: '/auth/vk/:type',
            component: VKAuth,
            name: 'vkauth'
         },
         {
            path: '/oauth/:method/:driver',
            component: OAuth,
            name: 'oauth'
         },
         {
            path: '/auth/recovery',
            component: AuthRecovery,
            name: 'recovery',
            meta:{
               title: 'Восстановление пароля'
            }
         },
         {
            path: '/password/reset/:token',
            component: AuthRecovery,
            name: 'reset'
         },
         {
            path: '/shop/:shop?',
            component: Shop,
            name: 'shop',
            meta: {
               auth: true,
               title: 'Магазин блоков'
            },
         }
      ]
   },
   {
      path: '/',
      component: WideContainer,
      children: [
         {
            path: '/cabinet',
            component: Cabinet,
            meta: {
               auth: true,
               title: 'Личный кабинет'
            },
            children: [
               {
                  path: '/',
                  component: CabinetIndex,
                  name: 'cabinet',
               },
               {
                  path: '/cabinet/donate',
                  component: CabinetDonate,
                  name: 'donate'
               },
               {
                  path: '/cabinet/kits',
                  component: CabinetKits,
                  name: 'kits'
               },
               {
                  path: '/cabinet/profile',
                  component: CabinetProfile,
                  name: 'profile'
               },
               {
                  path: '/cabinet/security',
                  component: CabinetSecurity,
                  name: 'security'
               },
               {
                  path: '/cabinet/punish',
                  component: CabinetPunish,
                  name: 'punish'
               },
            ]
         },
         {
            path: '/user/:login',
            component: UserProfile,
            name: 'user',
            meta: {
               title: 'Профиль игрока'
            }
         },
         {
            path: '/forum/discussion/:slug/:page?',
            component: ForumDiscussion,
            name: 'discussion'
         },
      ]
   },
   {
      path: '/forum',
      component: ForumContainer,
      children: [
         {
            path: '/',
            component: Forum,
            name: 'forum',
            meta: {
               title: 'Форум',
               metaTags: [
                  {
                     name: 'description',
                     content: 'Общение с администрацией и другими игроками проекта'
                  }
               ]
            }
         },
         {
            path: 'search',
            component: ForumSearch,
            name: 'forum_search'
         },
         {
            path: 'team',
            component: ForumAdmins,
            name: 'forum_team'
         },
         {
            path: 'category/:slug/:page?',
            component: ForumCategory,
            name: 'category'
         },
         {
            path: 'category/:category/create',
            component: ForumCreate,
            name: 'create_discussions',
            meta: {
               auth: true,
               title: 'Новая тема'
            },
         }
      ]
   },
   {
      path: '/email/confirm/:token',
      component: EmailConfirm,
      name: 'email_confirm'
   },
   {
      path: '*',
      component: ErrorNotFound,
      name: '404'
   }
]
