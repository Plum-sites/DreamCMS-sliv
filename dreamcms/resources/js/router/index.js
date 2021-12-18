import Vue from 'vue'
import Router from 'vue-router'

import defaultRoutes from './default';

Vue.use(Router);

export default new Router({
	mode: 'history',
	routes: defaultRoutes,
	scrollBehavior (to, from, savedPosition) {
		if (savedPosition) {
			return savedPosition
		} else {
			return { x: 0, y: 0 }
		}
	}
})
