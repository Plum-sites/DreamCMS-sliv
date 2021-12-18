<!-- App Main Structure -->
<template>
	<div class="app-default-layout">
		<template v-if="!isLoaded">
			<rotate-square2></rotate-square2>
		</template>
		<template v-else>
			<!-- App Header -->
			<app-header></app-header>
			<!-- App Main Content -->
			<v-content>
				<!-- App Router -->
				<transition name="router-anim" :enter-active-class="`animated ${selectedRouterAnimation}`">
					<slot></slot>
					<router-view></router-view>
				</transition>
			</v-content>
			<!-- app customizer -->
			<app-customizer></app-customizer>
		</template>
	</div>
</template>

<script>
import { mapGetters } from "vuex";
import Header from "Components/Header/Header.vue";
import Customizer from "Components/Customizer/Customizer";

export default {
  components: {
    appHeader: Header,
    appCustomizer: Customizer
  },
  computed: {
    ...mapGetters(["selectedRouterAnimation", "isLoaded"])
  }
};
</script>

<style scoped>
.app-default-layout {
  height: 100vh;
}
</style>
