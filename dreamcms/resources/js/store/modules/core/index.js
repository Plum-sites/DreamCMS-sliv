import * as Sentry from '@sentry/browser';
import api, {AUTH_LOGOUT, AUTH_SUCCESS, LOAD_USER, READ_NOTIFICATIONS} from '../../../api'

const state = {
    loaded: false,
    user: null,
    role: {},
    roles: {},
    servers: [],
    dgroups: [],
    permissions: [],
    bans: [],
    unreadNotifications: 0,
    record: 0,
    otp: false,

    loading: false,

    integration_urls: [],

    token: localStorage.getItem('api-token') || ''
};

const getters = {
    isLoaded: state => state.loaded,
    user: state => state.user,
    role: state => state.role,
    roles: state => state.roles,
    servers: state => state.servers,
    dgroups: state => state.dgroups,
    bans: state => state.bans,
    otp: state => state.otp,
    record: state => state.record,

    userPermissions: state => state.permissions,

    unreadNotifications: state => state.unreadNotifications,

    isLogged: state => state.user != null,

    accessToken: state => state.token,

    integrationUrls: state => state.integration_urls,
};

const actions = {
    [LOAD_USER]: ({commit, dispatch}) => {
        if (state.loading){
            return new Promise((resolve, reject) => {
                resolve();
            });
        }

        state.loading = true;
        return new Promise((resolve, reject) => {
            api
                .post("core/load")
                .then(function(response) {
                    commit(LOAD_USER, response.data);
                    resolve(response);
                })
                .catch(function(error) {
                    console.log(error);
                    reject(error);
                });
        });
    },
    [AUTH_SUCCESS]: ({commit, dispatch}, token) => {
        localStorage.setItem('api-token', token);
        window.axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;

        commit(AUTH_SUCCESS, token);
    },
    [AUTH_LOGOUT]: () => {
        localStorage.removeItem('api-token');
        window.axios.defaults.headers.common['Authorization'] = '';
        window.location.reload();
    },
    [READ_NOTIFICATIONS]: ({commit, dispatch}) => {
        commit(READ_NOTIFICATIONS);
    },
};

const mutations = {
    [LOAD_USER] (state, data) {
        state.user = data.user;
        state.role = data.role;
        state.roles = data.roles;
        state.servers = data.servers;
        state.dgroups = data.dgroups;
        state.otp = data.otp;
        state.bans = data.bans;
        state.permissions = data.permissions;
        state.unreadNotifications = data.unreadNotifications;
        state.record = data.record;
        state.integration_urls = data.integration_urls;

        state.loaded = true;
        state.loading = false;

        if (data.user){
            Sentry.setUser({
                "login": data.user.login,
                "email": data.user.email,
            });
        }
    },
    [AUTH_SUCCESS]: (state, token) => {
        state.token = token;
    },
    [READ_NOTIFICATIONS]: (state) => {
        state.unreadNotifications = 0;
    },
};

export default {
    state,
    getters,
    actions,
    mutations
}
