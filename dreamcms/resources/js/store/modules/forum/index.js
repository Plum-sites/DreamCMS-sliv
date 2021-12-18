import api, {FORUM_LOAD, FORUM_LOAD_LATEST, FORUM_LOAD_LEADERS, FORUM_LOAD_POPULARS} from '../../../api'

const state = {
    lastPosts: [],
    leaders: [],
    populars: [],

    categories: []
};

const getters = {
    lastPosts: state => state.lastPosts,
    leaders: state => state.leaders,
    populars: state => state.populars,

    forumCategories: state => state.categories,
};

const actions = {
    [FORUM_LOAD_LATEST]: ({commit, dispatch}) => {
        api
            .get("forum/latest")
            .then(function(response) {
                commit(FORUM_LOAD_LATEST, response.data);
            })
            .catch(function(error) {
                console.log(error);
            });
    },
    [FORUM_LOAD_LEADERS]: ({commit, dispatch}) => {
        api
            .get("forum/leaders")
            .then(function(response) {
                commit(FORUM_LOAD_LEADERS, response.data);
            })
            .catch(function(error) {
                console.log(error);
            });
    },
    [FORUM_LOAD_POPULARS]: ({commit, dispatch}) => {
        api
            .get("forum/populars")
            .then(function(response) {
                commit(FORUM_LOAD_POPULARS, response.data);
            })
            .catch(function(error) {
                console.log(error);
            });
    },


    [FORUM_LOAD]: ({commit, dispatch}) => {
        api
            .get("forum")
            .then(function(response) {
                commit(FORUM_LOAD, response.data);
            })
            .catch(function(error) {
                console.log(error);
            });
    }
};

const mutations = {
    [FORUM_LOAD_LATEST] (state, data) {
        state.lastPosts = data.posts;
    },
    [FORUM_LOAD_LEADERS] (state, data){
        state.leaders = data.leaders;
    },
    [FORUM_LOAD_POPULARS] (state, data){
        state.populars = data.populars;
    },


    [FORUM_LOAD] (state, data){
        state.categories = data.categories;
    }
};

export default {
    state,
    getters,
    actions,
    mutations
}
