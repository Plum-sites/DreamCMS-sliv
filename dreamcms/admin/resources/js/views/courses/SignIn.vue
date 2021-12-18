<template>
   <div class="courses-signin">
      <page-title-bar></page-title-bar>
      <v-container fluid grid-list-xl>
         <course-banner></course-banner>
         <v-layout row wrap align-center justify-center signIn-wrap>
            <v-flex xs12 sm12 md10 lg10 xl9>
               <div class="shop-by-category section-gap pad-y-lg">
                  <v-layout row wrap justify-center>
                     <v-flex xs11 sm8 md6 lg6 xl6>
                        <app-card
                           contentCustomClass="pt-0"
                           :heading="$t('message.userSignIn')"
                        >
                           <v-form>
                              <div class="mrgn-b-md">
                                 <v-text-field
                                 placeholder="Email*"
                                    type="email"
                                    required
                                 >
                                 </v-text-field>
                              </div>
                              <div class="mrgn-b-md">
                                 <v-text-field
                                    placeholder="Password*"
                                    type="password"
                                    required
                                 >
                                 </v-text-field>
                              </div>
                              <v-layout row wrap align-start justify-center mx-0>
                                 <v-checkbox :label="`Remember Me`"></v-checkbox>
                                 <router-link to="/session/forgot-password" class="mt-3">
                                    Forgot Password ?
                                 </router-link>
                              </v-layout>
                              <v-btn color="error" class="danger mx-0 mb-4"  to="/session/login">{{$t('message.signIn')}}</v-btn>
                              <p>Don't have an account? <router-link to="/session/sign-up" class="primary-text">Click here to create one</router-link></p>
                           </v-form>
                        </app-card>
                     </v-flex>
                     <v-flex xs11 sm8 md6 lg6 xl6>
                        <app-card :heading="$t('message.guestCheckout')"  contentCustomClass="pt-0">
                           <p>Proceed to checkout and create an account later.</p>
                           <v-btn 
                              class="error mx-0"
                              :to="`/${getCurrentAppLayoutHandler() + '/courses/payment'}`"
                           >{{$t('message.continueAsGuest')}}</v-btn>
                        </app-card>
                     </v-flex>
                  </v-layout>
               </div>    
            </v-flex>
         </v-layout>
      </v-container>
   </div>
</template>

<script>
import CourseBanner from "./CourseWidgets/CourseBanner";
import { getCurrentAppLayout } from "Helpers/helpers";
export default {
   components:{
		CourseBanner
   },
   methods: {
		getCurrentAppLayoutHandler() {
			return getCurrentAppLayout(this.$router);
    	}
  	}
}
</script>
