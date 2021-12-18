<template>
	<div class="hover-wrapper">
		<page-title-bar></page-title-bar>
		<v-container grid-list-xl pt-0>
			<v-layout row wrap>
				<app-card
					colClasses="xl12 lg12 md12 sm12 xs12"
				>
               <div class="mb-4">
						<p>The <code>v-timeline</code> is useful for stylistically displaying chronological information.</p>
					</div>
               <v-timeline>
               	<v-timeline-item
                     v-for="n in 4"
                     :key="n"
                     color="red lighten-2"
                     large
                  >
                  	<span slot="opposite">Tus eu perfecto</span>
                     <v-card class="elevation-2">
                     	<v-card-title class="headline">Lorem ipsum</v-card-title>
                        <v-card-text>
                           Lorem ipsum dolor sit amet, no nam oblique veritus. Commune scaevola imperdiet nec ut, sed euismod convenire principes at. Est et nobis iisque percipit, an vim zril disputando voluptatibus, vix an salutandi sententiae.
                        </v-card-text>
                     </v-card>
                  </v-timeline-item>
               </v-timeline>
            </app-card>            
		   </v-layout>
	   </v-container>
   </div>
</template>
