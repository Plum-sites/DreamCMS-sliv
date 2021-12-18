import Vue from 'vue'
import Vuex from 'vuex'

// modules
import settings from './modules/settings';
import sidebar from './modules/sidebar';
import core from './modules/core';

Vue.use(Vuex);

export const store = new Vuex.Store({
    modules: {
        settings,
        sidebar,
        core
    }
})
