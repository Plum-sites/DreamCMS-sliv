import Vue from 'vue'
import Router from 'vue-router'

//routes
import defaultRoutes from './default';

Vue.use(Router)

export default new Router({
	mode: 'history',
	routes: [
		defaultRoutes,
	]
})
