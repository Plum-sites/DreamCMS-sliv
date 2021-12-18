import $ from 'jquery';
import scplugin from "./scplugin";
import Vue from 'vue'
import BootstrapVue from 'bootstrap-vue'
import moment from 'moment-timezone'
import VueSocketIO from 'vue-socket.io'
import vSelect from 'vue-select'
import Notifications from 'vue-notification'

import App from './App.vue'
import router from './router'
import { store } from './store/store';
// import * as Sentry from '@sentry/browser';
// import * as Integrations from '@sentry/integrations';

// Sentry.init({
//     dsn: 'https://e435a170f3994e399cba73cc4ce345aa@sentry.beshelmek.org/6',
//     integrations: [new Integrations.Vue({Vue, attachProps: true, logErrors: true})],
// });

Vue.use(scplugin);

require('./auth');

global.jQuery = $;
global.$ = $;
window.$ = $;

Vue.component('v-select', vSelect);

import { VueReCaptcha } from 'vue-recaptcha-v3'
import {LOAD_USER} from "./api";

Vue.use(BootstrapVue);
Vue.use(Notifications);
Vue.use(VueReCaptcha, {
    siteKey: '6LeedroUAAAAAK2RUkaNLVBYraeQXNVHX45O227A',
    loaderOptions: {
        useRecaptchaNet: true,
        autoHideBadge: true,
    }
});
Vue.use(new VueSocketIO({
    debug: true,
    connection: 'https://' + window.location.hostname,
    vuex: {
        store,
        actionPrefix: 'SOCKET_',
        mutationPrefix: 'SOCKET_'
    },
    options: { path: "/nodejs/" }
}));

moment.locale('ru');
moment.tz.setDefault("Europe/Moscow");
Vue.prototype.moment = moment;

router.beforeEach((to, from, next) => {
    const nearestWithTitle = to.matched.slice().reverse().find(r => r.meta && r.meta.title);

    const nearestWithMeta = to.matched.slice().reverse().find(r => r.meta && r.meta.metaTags);
    const previousNearestWithMeta = from.matched.slice().reverse().find(r => r.meta && r.meta.metaTags);

    if(nearestWithTitle) document.title = nearestWithTitle.meta.title;

    Array.from(document.querySelectorAll('[data-vue-router-controlled]')).map(el => el.parentNode.removeChild(el));

    if(!nearestWithMeta) return next();

    nearestWithMeta.meta.metaTags.map(tagDef => {
        const tag = document.createElement('meta');

        Object.keys(tagDef).forEach(key => {
            tag.setAttribute(key, tagDef[key]);
        });

        tag.setAttribute('data-vue-router-controlled', '');

        return tag;
    }).forEach(tag => document.head.appendChild(tag));

    next();
});

router.beforeEach((to, from, next) => {
    if (to.matched.some(record => record.meta.auth) && !store.getters.isLogged) {
        store.dispatch(LOAD_USER).then(response => {
            if (!store.getters.isLogged) {
                store.dispatch('setLoginModal', true);
                next({name: 'news'});
            } else {
                next()
            }
        });
    }else {
        next()
    }
});

new Vue({
    sockets: {
        connect: function () {
            var token = store.getters.accessToken;
            if (token && token.length > 0){
                this.$socket.emit('authenticate', {token: store.getters.accessToken});
            }
        },
        authenticated: function () {
            //console.log("SocketIO JWT success authenticated!");
        },
        message: (msg) => {
            Vue.notify({
                title: msg.title,
                text: msg.msg,
                type: msg.type
            });
        }
    },
    store,
    router,
    render: h => h(App),
    components: {
        App,
    }
}).$mount('#app');
