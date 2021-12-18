<template>
   <app-card 
      class="course-info-wrap"
      :heading="$t('message.whatYoWillLearn')"
      colClasses="xs12 sm12 md12 lg12 xl12"
      contentCustomClass="pt-0"
   >
      <div class="course-info-box">
         <ul class="info-box-list">
            <li v-for= "(info, key) in CourseData.courseDetail.learn" :key="key"><i class="material-icons">check</i><span>{{info}}</span></li>
         </ul>
      </div>
   </app-card>
</template>

<script>
import CourseData from "../data";
export default {
   data() {
		return{
         CourseData
      }
   }
}
</script>