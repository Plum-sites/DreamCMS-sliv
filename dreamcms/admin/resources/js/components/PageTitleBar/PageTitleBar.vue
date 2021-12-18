<template>
	<v-container fluid>
		<app-card :fullBlock="true">
			<v-breadcrumbs :items="breadcrumbItems" v-if="breadcrumbItems != null">
			  <template slot="item" slot-scope="props">
			    <h2 class="text-capitalize mb-0">{{$t(props.item.title)}}</h2>
				  <div class="spacer"></div>
				  <v-breadcrumbs-item>
					  {{props.item.breadcrumb[0].breadcrumbInactive}}
				  </v-breadcrumbs-item>
					 <v-breadcrumbs-item disabled>
					  {{props.item.breadcrumb[1].breadcrumbActive}}
				  </v-breadcrumbs-item>
			  </template>
			</v-breadcrumbs>
		</app-card>
	</v-container>
</template>

<script>
export default {
	data() {
		return{
			breadcrumbItems: []
		}
	},
	created() {
		this.breadcrumbItems[0] = this.$breadcrumbs[0].meta;
	}
};
</script>
