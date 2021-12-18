import api, { LOAD_USER, READ_NOTIFY } from '../../api'

const state = {
  loaded: false,
  user: {},
  role: {},
  roles: {},
  notifications: [],
  servers: [],
  dgroups: [],
  permissions: [],

  token: localStorage.getItem('api-token') || '',
}

const getters = {
  isLoaded: state => state.loaded,
  user: state => state.user,
  role: state => state.role,
  roles: state => state.roles,
  servers: state => state.servers,
  dgroups: state => state.dgroups,
  userPermissions: state => state.permissions,

  notifications: state => state.notifications,

  accessToken: state => state.token,
}

const actions = {
  [LOAD_USER]: ({ commit, dispatch }) => new Promise((resolve, reject) => {
    api
      .post('core/load')
      .then(response => {
        commit(LOAD_USER, response.data)
        resolve(response)
      })
      .catch(error => {
        console.log(error)
        reject(error)
      })
  }),
  [READ_NOTIFY]: () => {
    api
      .post('notifications/read')
      .then(response => {
        // commit(READ_NOTIFY, response.data);
      })
      .catch(error => {
        console.log(`Error ${error}`)
      })
  },
}

const mutations = {
  [LOAD_USER](state, data) {
    state.user = data.user
    state.role = data.role
    state.roles = data.roles
    state.servers = data.servers
    state.dgroups = data.dgroups
    state.permissions = data.permissions

    state.loaded = true
  },
}

export default {
  state,
  getters,
  actions,
  mutations,
}
