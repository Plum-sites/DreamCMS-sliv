import Vue from 'vue'
import Router from 'vue-router'

const ComingSoon = () => import('./views/ComingSoon')

const Dashboard = () => import('./views/Dashboard')
const DashboardProfit = () => import('./views/DashboardProfit')

const Refer = () => import('./views/Refer')
const CRUD = () => import('./views/CRUD')
const Manager = () => import('./views/Manager')
const ACL = () => import('./views/ACL')
const Online = () => import('./views/Online')
const RCON = () => import('./views/chat/Chat')
const Logs = () => import('./views/Logs')
const ForumManager = ComingSoon
const ModerEntry = ComingSoon

/* const Dashboard = () => import('./views/Dashboard');
const DashboardProfit = () => import('./views/DashboardProfit');
const Refer = () => import('./views/Refer');
const CRUD = () => import('./views/CRUD');
const ACL = () => import('./views/ACL');
const Manager = () => import('./views/Manager');
const ForumManager = () => import('./views/ForumManager');
const ModerEntry = () => import('./views/ModerEntry');
const Online = () => import('./views/Online');
const RCON = () => import('./views/RCON');
const Error = () => import('./views/Error');
const Logs = () => import('./views/Logs'); */

Vue.use(Router)

const router = new Router({
  mode: 'history',
  base: '/admin',
  scrollBehavior() {
    return { x: 0, y: 0 }
  },
  routes: [
    {
      path: '/userlogs',
      component: Logs,
      name: 'userlogs',
      meta: {
        permission: 'admin.dashboard.access',
        title: 'Логи',
      },
    },
    {
      path: '/dashboard',
      component: Dashboard,
      name: 'dashboard'
    },
    {
      path: '/dashboard/profit',
      component: DashboardProfit,
      name: 'dashboard_profit',
      meta: {
        permission: 'admin.dashboard_profit.access'
      },
    },
    {
      path: '/refer',
      component: Refer,
      name: 'refer',
      meta: {
        permission: 'admin.refer.access',
        title: 'Реферальная система',
      },
    },
    {
      path: '/crud/:model',
      component: CRUD,
      name: 'crud',
      meta: {
        title: 'CRUD',
      },
    },
    {
      path: '/acl',
      component: ACL,
      name: 'acl',
      meta: {
        permission: 'admin.acl.access',
        title: 'ACL',
      },
    },
    {
      path: '/manager',
      component: Manager,
      name: 'manager',
      meta: {
        permission: 'admin.manager.access',
        title: 'Менеджер',
      },
    },
    {
      path: '/forum/manager',
      component: ForumManager,
      name: 'forum_manager',
      meta: {
        permission: 'admin.forum_manager.access',
        title: 'Форумный менеджер',
      },
    },
    {
      path: '/moder',
      component: ModerEntry,
      name: 'moder_entry',
      meta: {
        permission: 'admin.moder_entry.access',
        title: 'Заявки на модераторов',
      },
    },
    {
      path: '/online',
      component: Online,
      name: 'online',
      meta: {
        permission: 'admin.online.access',
        title: 'Трекер онлайна',
      },
    },
    {
      path: '/servers/rcon',
      component: RCON,
      name: 'rcon',
      meta: {
        contentRenderer: 'sidebar-left',
        contentClass: 'chat-application',
        permission: 'admin.rcon.access',
        title: 'RCON',
      },
    },
    {
      path: '/pages/error-404',
      name: 'page-error-404',
      component: () => import('./views/error/Error404.vue'),
    },
    {
      path: '*',
      redirect: '/pages/error-404',
    },
  ],
})

export default router
