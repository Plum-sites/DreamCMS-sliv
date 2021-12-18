import Vue from 'vue'
import { ToastPlugin, ModalPlugin } from 'bootstrap-vue'
import VueCompositionAPI from '@vue/composition-api'
import scplugin from './scplugin'

import router from './router'
import store from './store'
import App from './App.vue'

// Global Components
import './global-components'

// 3rd party plugins
import '@/libs/portal-vue'
import '@/libs/toastification'
import '@/libs/sweet-alerts'

// BSV Plugin Registration
Vue.use(ToastPlugin)
Vue.use(ModalPlugin)

// Composition API
Vue.use(VueCompositionAPI)

// import core styles
require('@core/scss/core.scss')

// import assets styles
require('@/assets/scss/style.scss')

Vue.config.productionTip = false

// axios
import axios from './api'
import {LOAD_USER} from "./api";

Vue.use(scplugin)
Vue.prototype.$http = axios

window.Vue = Vue

router.beforeEach((to, from, next) => {
  if (to.matched.some(record => record.meta.permission)) {
    if (!store.getters.isLogged){
      store.dispatch(LOAD_USER).then(response => {
        if (store.getters.userPermissions.includes(to.meta.permission)){
          next()
        }else {
          next({
            name: 'error',
            params: {
              type: to.meta.permission
            }
          })
        }
      });
    }else {
      if (store.getters.userPermissions.includes(to.meta.permission)){
        next()
      }else {
        next({
          name: 'error',
          params: {
            type: to.meta.permission
          }
        })
      }
    }
  }else{
    next()
  }
});

new Vue({
  router,
  store,
  render: h => h(App),
}).$mount('#app')
