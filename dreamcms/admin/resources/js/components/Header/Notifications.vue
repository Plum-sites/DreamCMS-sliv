<template>
	<v-menu offset-y left origin="right top" z-index="99" content-class="notification-dropdown" transition="slide-y-transition" nudge-top="-10">
		<v-btn class="notification-icon ma-0"  icon large slot="activator" v-on:click="markAsRead">
			<i :class="'zmdi grey--text zmdi-notifications-active ' + (notifications.length > 0 ? 'animated infinite wobble' : '') + ' zmdi-hc-fw font-lg'"></i>
		</v-btn>
		<div class="dropdown-content">
			<div class="dropdown-top d-custom-flex justify-space-between primary">
				<span class="white--text fw-bold">Уведомления</span>
				<span class="v-badge warning">{{ notifications.length }}</span>
			</div>
			<v-list class="dropdown-list">
				<v-list-tile v-for="notification in notifications" :key="notification.title" @click="">
					<i class="mr-3 zmdi" :class="notification.icon"></i>
					<span>{{ $t(notification.title) }}</span>
				</v-list-tile>
			</v-list>
		</div>
	</v-menu>
</template>

<script>
	import {mapGetters} from "vuex";
	import {READ_NOTIFY} from "../../api";

	export default {
		computed:{
			...mapGetters([
				"notifications"
			])
		},
		methods: {
			markAsRead: function () {
				this.$store.dispatch(READ_NOTIFY);
			}
		}
	};
</script>
