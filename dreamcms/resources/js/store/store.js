import Vue from 'vue'
import Vuex from 'vuex'

// modules
import core from './modules/core';
import header from './modules/header';
import forum from './modules/forum';
import modals from './modules/modals';

Vue.use(Vuex);

export const store = new Vuex.Store({
    modules: {
        core,
        header,
        forum,
        modals
    }
});
