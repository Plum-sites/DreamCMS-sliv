<template class="custom-flex">
   <div class="flex xs12 sm12 md12 lg12 xl12 courses-overview">
      <v-expansion-panel v-for="(list,key) in  CourseData.courseDetail.topics" :key="key">
			<v-expansion-panel-content>
				<div slot="header" class="layout mt-0 justify-start">
					{{list.name}}
				</div>
				<v-list v-for="(details,key) in list.courseDetail" :key="key">
					<v-list-tile  :to="`/${getCurrentAppLayoutHandler() + '/courses/courses-detail'}`">
						<v-list-tile-action>
							<v-icon>play_circle_filled</v-icon>	
						</v-list-tile-action>
						<v-list-tile-content>
							<v-list-tile-title  class="ma-0 layout row wrap justify-space-between">
								<v-flex xs12 sm12 md12 lg12 xl8 py-0 pl-0>
								{{details.name}}
								</v-flex>
								<v-flex xs12 sm12 md12 lg12 xl4 py-0>
									<div class="ma-0 layout row wrap justify-space-between">
										<div><a href="#" class="fs-12 fw-normal">Preview</a></div>
										<div><span class="fs-12 fw-normal">{{details.time}}</span></div>
									</div>
								</v-flex>
							</v-list-tile-title>
						</v-list-tile-content>
					</v-list-tile>
					<v-divider></v-divider>
				</v-list>
			</v-expansion-panel-content>
      </v-expansion-panel>
   </div>
</template>

<script>
import CourseData from "../data";
import { getCurrentAppLayout } from "Helpers/helpers";

export default {
   data() {
		return {
         CourseData
      }
   },
	methods: {
		getCurrentAppLayoutHandler() {
			return getCurrentAppLayout(this.$router);
    	}
  	}
}
</script>