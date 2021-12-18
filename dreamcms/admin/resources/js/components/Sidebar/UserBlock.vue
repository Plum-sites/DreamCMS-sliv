<template>
   <v-list-tile class="sidebar-profile">
		<v-list-tile-avatar>
			<img :src="'/head/' + user.uuid + '/100'" alt="avatar" height="40" width="40" class="img-responsive" />
		</v-list-tile-avatar>
		<v-list-tile-content class="ml-3">
			<v-list-tile-title><span>{{ user.login }}</span></v-list-tile-title>
		</v-list-tile-content>
		<v-menu 
			bottom
			offset-y
			left
			content-class="userblock-dropdown" 
			nudge-top="-10"
			nudge-right="0"
			transition="slide-y-transition"
		>
			<v-btn dark icon slot="activator" class="ma-0">
					<v-icon>more_vert</v-icon>
			</v-btn>
			<div class="dropdown-content">
				<div class="dropdown-top white--text primary">
					<span class="white--text fs-14 fw-bold d-block">{{ user.login }}</span>
					<span class="d-block fs-12 fw-normal">{{ user.email }}</span>
				</div>
			</div>
		</v-menu>
   </v-list-tile>
</template>

<script>
import { getCurrentAppLayout } from "Helpers/helpers";
import {mapGetters} from "vuex";

export default {
	computed: {
		...mapGetters([
			"user", "role", "roles"
		])
	},
  methods: {

	 getMenuLink(path) {
		 return '/' + getCurrentAppLayout(this.$router) +  path;
	 }
  }
};
</script>
