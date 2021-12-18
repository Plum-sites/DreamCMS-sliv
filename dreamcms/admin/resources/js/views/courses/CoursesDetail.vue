<template>
   <div class="courses-detail-wrap" >
      <page-title-bar></page-title-bar>
      <v-container grid-list-xl fluid>
			<course-detail-banner></course-detail-banner>
			<div class="course-detail">
            <v-layout row wrap justify-center align-center>
               <v-flex xs12 sm12 md12 lg12 xl12>
                  <v-layout row wrap>
                     <v-flex xs12 sm12 md12 lg9 xl9>
                        <v-layout row wrap>
                           <course-detail-learn></course-detail-learn>
                           <course-detail-desciption></course-detail-desciption>  
                           <course-detail-overview></course-detail-overview>
                           <course-detail-instructor></course-detail-instructor>
                           <app-card 
                              colClasses="xs12 sm12 md12 lg12 xl12"
                              customClasses="more-courses-grid"
                              contentCustomClass="layout row wrap pt-0"
                              :heading="$t('message.moreCoursesFromJamesColt')"
                           >                     
                              <v-layout row wrap align-start justify-start mx-0 mb-0>
                                 <div class="course-item-wrap">
                                    <course-card 
                                       :height="200"
                                       :width="335"
                                       :data="CourseData.courseDetail.moreCourses"
                                       :cols="6" :colxl="4" :collg="4" :colmd="4" :colsm="6" :colxs="12"
                                    ></course-card>
                                 </div>
                              </v-layout>
                           </app-card>
                        </v-layout>
                     </v-flex>
                     <v-flex xs12 sm12 md12 lg3 xl3 course-sidebar>
                        <div class="custom-height-auto">
                           <course-detail-billing></course-detail-billing>
                           <app-card 
                              contentCustomClass="pt-0"
                              heading="Contrary to popular belief ?"
                           >
                              <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                              <a href="#" class="font-weight-bold primary-text">Nulla eu augue !</a>
                           </app-card>
                        </div>
                     </v-flex>
                  </v-layout>
					</v-flex>
            </v-layout>
			</div>
      </v-container>
   </div>
</template>

<script>
import CourseBanner from "./CourseWidgets/CourseBanner";
import CourseData from "./data";
import CourseDetailLearn from "./CourseWidgets/CourseDetailLearn";
import CourseDetailBanner from "./CourseWidgets/CourseDetailBanner";
import CourseDetailDesciption from "./CourseWidgets/CourseDetailDescription";
import CourseDetailOverview from "./CourseWidgets/CourseDetailOverview";
import CourseDetailInstructor from "./CourseWidgets/CourseDetailInstructor";
import CourseCard from "./CourseWidgets/CourseCard";
import CourseDetailBilling from "./CourseWidgets/CourseDetailBilling";
export default {
	data(){
		return{
			CourseData
		}
	},
   components:{
      CourseBanner,
		CourseDetailBanner,
		CourseDetailLearn,
		CourseDetailDesciption,
		CourseDetailOverview,
		CourseDetailInstructor,
		CourseCard,
		CourseDetailBilling
	}
}
</script>
