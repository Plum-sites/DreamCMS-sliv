<template>
   <app-card  
      class="detail-dec-wrap"
      colClasses="xs12 sm12 md12 lg12 xl12"
      :heading="$t('message.description')"
      contentCustomClass="pt-0"
   >
      <p class="font-weight-bold mrgn-b-sm">{{CourseData.courseDetail.description.title}}</p>
      <p>{{CourseData.courseDetail.description.content}}</p>
      <ul>
         <li v-for="(feature,key) of CourseData.courseDetail.description.features" :key="key">{{feature}}</li>
      </ul>
   </app-card>
</template>

<script>
import CourseData from "../data";
export default {
   data() {
		return {
         CourseData
      }
   }
}
</script>