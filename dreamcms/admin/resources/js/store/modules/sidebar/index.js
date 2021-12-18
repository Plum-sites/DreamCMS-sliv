/**
 * Sidebar Module
 */

import { menus } from './data.js';

const state = {
    menus
}

const getters = {
    menus: state => {
        return state.menus;
    }
}

const actions = {
    setActiveMenuGroup(context, payload) {
        context.commit('setActiveMenuGroupHandler', payload);
    }
}

const mutations = {
    setActiveMenuGroupHandler(state, router) {
        let fullPath = '';
        if(router.pathURL){
            fullPath = router.pathURL;
        }else{
            fullPath = router.history.current.fullPath;
        }
        let path = fullPath.split('/');
        let matchedPath = '/' + path[1] + '/' + path[2];
        for (const category in state.menus) {
            for(const menuGroup in state.menus[category]) {
                if(state.menus[category][menuGroup].items !== null) {
                    for(const matched in state.menus[category][menuGroup].items){
                        if(state.menus[category][menuGroup].items[matched].path == matchedPath || state.menus[category][menuGroup].items[matched].path == fullPath ){
                           state.menus[category][menuGroup].active = true;
                        }
                    }
                }
            }
        }
    }
}

export default {
    state,
    getters,
    actions,
    mutations
}