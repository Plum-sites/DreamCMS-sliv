import Full from 'Container/Full'

// dashboard components
const Dashboard = () => import('Pages/Dashboard');
const DashboardProfit = () => import('Pages/DashboardProfit');
const Refer = () => import('Pages/Refer');
const CRUD = () => import('Pages/CRUD');
const ACL = () => import('Pages/ACL');
const Manager = () => import('Pages/Manager');
const ForumManager = () => import('Pages/ForumManager');
const ModerEntry = () => import('Pages/ModerEntry');
const Online = () => import('Pages/Online');
const RCON = () => import('Pages/RCON');
const Error = () => import('Pages/Error');
const Logs = () => import('Pages/Logs');

export default {
   path: '/',
   component: Full,
   redirect: '/admin/dashboard',
   children: [
      {
         path: '/admin/error/:type',
         component: Error,
         name: 'error',
         meta: {
            title: 'Ошибка'
         }
      },
      {
         path: '/admin/logs',
         component: Logs,
         meta: {
            permission: 'admin.dashboard.access',
            title: 'Логи'
         }
      },
      {
         path: '/admin/dashboard',
         component: Dashboard,
         meta: {
            permission: 'admin.dashboard.access',
            title: 'message.dashboard'
         }
      },
      {
         path: '/admin/dashboard/profit',
         component: DashboardProfit,
         meta: {
            permission: 'admin.dashboard_profit.access',
            title: 'message.dashboard'
         }
      },
      {
         path: '/admin/refer',
         component: Refer,
         meta: {
            permission: 'admin.refer.access',
            title: 'Реферальная система'
         }
      },
      {
         path: '/admin/crud/:model',
         component: CRUD,
         name: 'crud',
         meta:{
            title: 'CRUD'
         }
      },
      {
         path: '/admin/acl',
         component: ACL,
         name: 'acl',
         meta:{
            permission: 'admin.acl.access',
            title: 'ACL'
         }
      },
      {
         path: '/admin/manager',
         component: Manager,
         name: 'manager',
         meta:{
            permission: 'admin.manager.access',
            title: 'Менеджер'
         }
      },
      {
         path: '/admin/forum/manager',
         component: ForumManager,
         name: 'fmanager',
         meta:{
            permission: 'admin.forum_manager.access',
            title: 'Форумный менеджер'
         }
      },
      {
         path: '/admin/moder',
         component: ModerEntry,
         name: 'moderentry',
         meta:{
            permission: 'admin.moder_entry.access',
            title: 'Заявки на модераторов'
         }
      },
      {
         path: '/admin/online',
         component: Online,
         name: 'online',
         meta:{
            permission: 'admin.online.access',
            title: 'Трекер онлайна'
         }
      },
      {
         path: '/admin/servers/rcon',
         component: RCON,
         name: 'rcon',
         meta:{
            permission: 'admin.rcon.access',
            title: 'RCON'
         }
      }
   ]
}
