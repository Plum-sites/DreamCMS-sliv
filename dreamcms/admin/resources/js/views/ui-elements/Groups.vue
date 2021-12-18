<template>
	<div class="hover-wrapper group-wrapper">
		<page-title-bar></page-title-bar>
		<v-container grid-list-xl pt-0>
			<v-layout row wrap>
				<app-card
					:heading="$t('message.buttonGroups')"
					customClasses="mb-20"
					colClasses="xl12 lg12 md12 sm12 xs12"
				>
					<div class="mb-4">
						<p>The <code>v-btn-toggle</code> ccomponent is a simple wrapper for <code>v-item-group</code> built specifically to work with <code>v-btn</code>.</p>
					</div>
					<v-toolbar dense>
                  <v-overflow-btn
                     :items="dropdown_font"
                     label="Select font"
                     hide-details
                     class="pa-0"
                  ></v-overflow-btn>

                  <v-divider vertical></v-divider>

                  <v-overflow-btn
                     :items="dropdown_edit"
                     editable
                     label="Select size"
                     hide-details
                     class="pa-0"
                     overflow
                  ></v-overflow-btn>

                  <v-divider
                     class="mr-2"
                     vertical
                  ></v-divider>

                  <v-btn-toggle
                     v-model="toggle_multiple"
                     class="transparent"
                     multiple
                  >
                     <v-btn :value="1" flat>
                        <v-icon>format_bold</v-icon>
                     </v-btn>

                     <v-btn :value="2" flat>
                        <v-icon>format_italic</v-icon>
                     </v-btn>

                     <v-btn :value="3" flat>
                        <v-icon>format_underlined</v-icon>
                     </v-btn>

                     <v-btn :value="4" flat>
                        <v-icon>format_color_fill</v-icon>
                     </v-btn>
                  </v-btn-toggle>

                  <v-divider
                     class="mx-2"
                     vertical
                  ></v-divider>

                  <v-btn-toggle
                     v-model="toggle_exclusive"
                     class="transparent"
                  >
                     <v-btn :value="1" flat>
                        <v-icon>format_align_left</v-icon>
                     </v-btn>

                     <v-btn :value="2" flat>
                        <v-icon>format_align_center</v-icon>
                     </v-btn>

                     <v-btn :value="3" flat>
                        <v-icon>format_align_right</v-icon>
                     </v-btn>

                     <v-btn :value="4" flat>
                        <v-icon>format_align_justify</v-icon>
                     </v-btn>
                  </v-btn-toggle>
               </v-toolbar>
				</app-card>
				<app-card
					:heading="$t('message.itemGroups')"
					customClasses="mb-20"
					colClasses="xl12 lg12 md12 sm12 xs12"
				>
					<div class="mb-4">
						<p>The <code>v-item-group</code> provides the ability to create a group of selectable items out of any component. This is the baseline functionality for components such as <code>v-tabs</code> and <code>v-carousel</code>.</p>
                  <v-card>
                     <v-toolbar
                        card
                        color="blue-grey"
                        dark
                     >
                        <v-toolbar-title>Submit a post</v-toolbar-title>
                     </v-toolbar>

                     <v-card-text>
                     	<v-text-field box label="Title" value="My new post"></v-text-field>
                        <v-textarea
									box
									label="Text"
									value="Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse"
                        ></v-textarea>

                        <v-divider class="my-2"></v-divider>

                        <v-item-group multiple>
									<v-subheader>Tags</v-subheader>
									<v-item
										v-for="n in 8"
										:key="n"
									>
										<v-chip
											slot-scope="{ active, toggle }"
											:selected="active"
											@click="toggle"
										>
											Tag {{ n }}
										</v-chip>
									</v-item>
                        </v-item-group>
                     </v-card-text>
                     <v-divider></v-divider>
                     <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn
                        color="success"
                        depressed
                        >
                        Post
                        </v-btn>
                     </v-card-actions>
                  </v-card>
					</div>
				</app-card>
            <app-card
					:heading="$t('message.windows')"
					customClasses="mb-20"
					colClasses="xl12 lg12 md12 sm12 xs12"
				>
               <div class="mb-4">
						<p>The <code>v-window</code> component is the baseline functionality for transitioning content from 1 pane to another. Other components such as <code>v-tabs</code>, <code>v-carousel</code> and <code>v-stepper</code> utilize this component at their core.</p>
					</div>
					<v-layout  align-center>
						<v-item-group
							v-model="window"
							class="mr-4 group"
							mandatory
							tag="v-flex"
						>
							<v-item
								v-for="n in length"
								:key="n"
							>
								<div slot-scope="{ active, toggle }">
									<v-btn
										:input-value="active"
										icon
										@click="toggle"
									>
										<v-icon>mdi-record</v-icon>
									</v-btn>
								</div>
							</v-item>
						</v-item-group>

						<v-flex>
							<v-window
								v-model="window"
								class="elevation-1"
								vertical
							>
								<v-window-item
									v-for="n in length"
									:key="n"
								>
									<v-card flat>
										<v-card-text>
											<v-layout align-center custom-layout mb-3>
												<v-avatar color="grey" class="mr-3"></v-avatar>
												<strong class="title">Title {{ n }}</strong>
												<v-spacer></v-spacer>
												<v-btn icon>
													<v-icon>mdi-account</v-icon>
												</v-btn>
											</v-layout>
											<p v-for="(n,index) in 3" :key="index">
												Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
											</p>
											</p>
										</v-card-text>
									</v-card>
								</v-window-item>
							</v-window>
						</v-flex>
					</v-layout>
            </app-card>
            <app-card
					:heading="$t('message.onBoarding')"
					customClasses="mb-20"
					colClasses="xl12 lg12 md12 sm12 xs12"
				>
               <div class="mb-4">
                  <p><code>v-window</code> makes it easy to create custom components such as a different styled stepper.</p>
               </div>
               <v-card
                  color="black"
                  dark
                  flat
                  tile
               >
                  <v-window v-model="onboarding">
                     <v-window-item
								v-for="n in length"
								:key="`card-${n}`"
                     >
								<v-card
									color="transparent"
									height="200" 
								>
									<v-layout 
										align-center
										justify-center
										fill-height m-0
										tag="v-card-text"
									>
										Transparent themed, for background-imaged slides. Background color black added for demonstration purposes.
									</v-layout>
								</v-card>
                     </v-window-item>
                  </v-window>

                  <v-card-actions class="justify-space-between group-slider-action">
                     <v-btn
								flat
								@click="prev" class="group-slider-prev"
                     >
                     	<v-icon>mdi-chevron-left</v-icon>
                     </v-btn>
                     <v-item-group
								v-model="onboarding"
								class="text-xs-center"
								mandatory
                     >
								<v-item
									v-for="n in length"
									:key="`btn-${n}`"
								>
									<v-btn
										slot-scope="{ active, toggle }"
										:input-value="active"
										icon
										@click="toggle"
									>
										<v-icon>mdi-record</v-icon>
									</v-btn>
								</v-item>
                     </v-item-group>
                     <v-btn
								flat
								@click="next" class="group-slider-next"
                     >
                     	<v-icon>mdi-chevron-right</v-icon>
                     </v-btn>
                  </v-card-actions>
               </v-card>
            </app-card>
            <app-card
					:heading="$t('message.accountCreation')"
					customClasses="mb-20"
					colClasses="xl12 lg12 md12 sm12 xs12"
				>
               <div class="mb-4">
                  <p>Create rich forms with smooth animations.<code> v-window </code> automatically tracks the current selection index to automatically change the transition direction. This can be manually controlled with the reverse prop.</p>
               </div>
               <v-card
						class="mx-auto"
						max-width="500"
					>
						<v-card-title class="title font-weight-regular justify-space-between">
							<span>{{ currentTitle }}</span>
							<v-avatar
								color="primary lighten-2"
								class="subheading white--text"
								size="24"
								v-text="step"
							></v-avatar>
						</v-card-title>
						<v-window v-model="step">
							<v-window-item :value="1">
								<v-card-text>
									<v-text-field
										label="Email"
										value="abc@example.com"
									></v-text-field>
									<span class="caption grey--text text--darken-1">
										This is the email you will use to login to your Vuely account
									</span>
								</v-card-text>
							</v-window-item>
							<v-window-item :value="2">
								<v-card-text>
									<v-text-field
										label="Password"
										type="password"
									></v-text-field>
									<v-text-field
										label="Confirm Password"
										type="password"
									></v-text-field>
									<span class="caption grey--text text--darken-1">
										Please enter a password for your account
									</span>
								</v-card-text>
							</v-window-item>

							<v-window-item :value="3">
								<div class="pa-3 text-xs-center">
									<v-img
										class="mb-3"
										contain
										height="128"
										src="/static/img/session.png"
									></v-img>
									<h3 class="title font-weight-light mb-2">Welcome to Vuely</h3>
									<span class="caption grey--text">Thanks for signing up!</span>
								</div>
							</v-window-item>
						</v-window>
						<v-divider></v-divider>
						<v-card-actions>
							<v-btn
								:disabled="step === 1"
								flat
								@click="step--"
							>
								Back
							</v-btn>
							<v-spacer></v-spacer>
							<v-btn
								:disabled="step === 3"
								color="primary"
								depressed
								@click="step++"
                        >
                        	Next
							</v-btn>
						</v-card-actions>
					</v-card>
            </app-card>
		   </v-layout>
	   </v-container>
   </div>
</template>

<script>
  export default {
    data () {
      return {
         length: 3,
         window: 0,
         step: 1,
         onboarding: 0,
         dropdown_font: [
          { text: 'Arial' },
          { text: 'Calibri' },
          { text: 'Courier' },
          { text: 'Verdana' }
        ],
        dropdown_edit: [
          { text: '100%' },
          { text: '75%' },
          { text: '50%' },
          { text: '25%' },
          { text: '0%' }
        ],
        toggle_exclusive: 2,
        toggle_multiple: [1, 2, 3]
      }
    },
    computed: {
      currentTitle () {
        switch (this.step) {
          case 1: return 'Sign-up'
          case 2: return 'Create a password'
          default: return 'Account created'
        }
      }
    },
     methods: {
      next () {
        this.onboarding = this.onboarding + 1 === length
          ? 0
          : this.onboarding + 1
      },
      prev () {
        this.onboarding = this.onboarding - 1 < 0
          ? this.length - 1
          : this.onboarding - 1
      }
    }
  }
</script>
