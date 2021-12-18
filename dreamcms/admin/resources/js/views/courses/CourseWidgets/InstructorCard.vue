<template>
   <div class="instructor-card-wrap layout wrap row">
      <app-card
			colClasses="xl2 lg4 md4 sm6 xs12"
         customClasses="h-100 text-xs-center"
         v-for="(instruct,key) in CourseData.instructors"
         :key="key"
      >	
         <a href="#" class="instructor-card">
		      <router-link :to="`/${getCurrentAppLayoutHandler() + '/dashboard/ecommerce'}`">
               <img :src="instruct.image" width="75" height="75" alt="instructer">
            </router-link>
            <div class="instructor-details">
               <span class="desig d-block fs-12 fw-normal">{{instruct.profile}}, {{instruct.place}}</span>
               <span class="name font-weight-bold d-block">{{instruct.name}}</span>
            </div>
            <div class="instructor-counts">
               <span class="course-count d-block fs-12 fw-normal">{{instruct.courses}} courses</span>
               <span class="student-count d-block fs-12 fw-normal"><span class="font-weight-bold">{{instruct.number}}</span> {{instruct.type}}</span>
            </div>
         </a>
      </app-card>
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

