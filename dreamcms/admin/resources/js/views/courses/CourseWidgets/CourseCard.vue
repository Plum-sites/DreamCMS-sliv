<template>
	<v-container>
	   <div class="course-card layout row wrap">
			<app-card 
				customClasses="course-item-wrap"
				v-for="(list,key) in data" :key="key"
				:colClasses="`xs${colxs} sm${colsm} md${colmd} lg${collg} xl${colxl}`"
			>
				<div class="image-wrap">
					<template v-if="list.videoDemoStatus == false">
						<router-link :to="`/${getCurrentAppLayoutHandler() + '/courses/courses-detail'}`">
							<img :src="list.image" alt="image">
						</router-link>
					</template>	
					<template  v-if="list.videoDemoStatus == true">
						<router-link :to="`/${getCurrentAppLayoutHandler() + '/courses/courses-detail'}`">
							<img :src="list.image" alt="image"> 
						</router-link>
						<v-dialog
							v-model="dialog"
							width="500"
							>
							<v-btn  slot="activator" icon>
								<v-icon>play_circle_filled</v-icon>
							</v-btn>
							<iframe  :src="list.demoVideoUrl" frameborder="0" allowfullscreen></iframe>
						</v-dialog>
					</template>	
					<template v-if="list.bestseller == true">
						<span class="best-seller bestseller-tag d-inline-block">{{$t('message.bestseller')}}</span>
					</template>
				</div>
				<router-link :to="`/${getCurrentAppLayoutHandler() + '/courses/courses-detail'}`" >
					<h4 class="make-ellipse">{{list.name}}</h4>
				</router-link>
				<span class="fs-12 mb-3 d-block">{{list.content}}</span>
				<div class="rating-text layout row wrap ma-0">
					<v-rating :value="list.rating" color="warning"  background-color="warning"></v-rating>
					<p>{{list.rating}} Stars</p>
				</div>
				<div class="price">
					<h4 color="primary">${{list.oldPrice*(100-list.disount)/100}}</h4>
					<del>${{list.oldPrice}}</del>
				</div>
				<app-card customClasses="course-hover-item">
					<div class="header">
						<div class="meta-info">
							<span class="date-info fs-12 fw-normal">Last updated: {{list.lastUpdates}}</span>
						</div>
						<div class="card-header"><h4><a href="#">{{list.name}}</a></h4></div>
						<div class="meta-info mb-1">
							<div class="mb-1" v-if = "list.bestseller == true">
								<span class="category fs-12 fw-normal"><span class="bestseller-tag">bestseller</span> {{list.bestSell}}</span>
							</div>
							<span class="meta-info-block">                                             
								<span class="lectures fs-12 fw-normal"><v-icon class="cmr-8">play_circle_filled</v-icon>{{list.lectures}} lectures</span>
								<span class="durations fs-12 fw-normal"><v-icon class="cmr-8">access_time</v-icon>{{list.hours}} hours</span>
								<span class="durations fs-12 fw-normal"><v-icon class="cmr-8">show_chart</v-icon>{{list.level}} Levels</span>
							</span>
						</div>
					</div>
					<div class="short-desc">
						<span>{{list.describe}}</span>
						<ul class="course-list  my-3">
							<li v-for="(feature,key) of list.features" :key="key">{{feature}}</li>
						</ul>
					</div>
					<v-btn 
						class="error" block 
						:to="`/${getCurrentAppLayoutHandler() + '/courses/sign-in'}`"
					>{{$t('message.addToCart')}}</v-btn>
				</app-card>
			</app-card>
		</div>
	</v-container>
</template>

<script>
import CourseData from "../data";
import { getCurrentAppLayout } from "Helpers/helpers";
export default {
	props: ['data','colxs','colsm','colmd','collg','colxl','width','height'],
   data() {
		return {
			dialog: false,
         CourseData,
      }
	},
	methods: {
		getCurrentAppLayoutHandler() {
			return getCurrentAppLayout(this.$router);
    	}
  	}
}
</script>
