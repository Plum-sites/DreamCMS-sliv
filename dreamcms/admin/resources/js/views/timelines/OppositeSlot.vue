<template>
	<div class="hover-wrapper">
		<page-title-bar></page-title-bar>
		<v-container grid-list-xl pt-0>
			<v-layout row wrap>				
            <app-card
					colClasses="xl12 lg12 md12 sm12 xs12"
				>
               <div class="mb-4">
						<p>The opposite slot provides an additional layer of customization within your timelines.</p>
					</div>
               <v-timeline>
                  <v-timeline-item
                     v-for="(year, i) in years"
                     :color="year.color"
                     :key="i"
                     small
                  >
                     <span
                        slot="opposite"
                        :class="`headline font-weight-bold ${year.color}--text`"
                        v-text="year.year"
                     ></span>
                     <div class="py-3">
                        <h2 :class="`headline font-weight-light mb-3 ${year.color}--text`">Lorem ipsum</h2>
                        <div>
                           Lorem ipsum dolor sit amet, no nam oblique veritus. Commune scaevola imperdiet nec ut, sed euismod convenire principes at. Est et nobis iisque percipit, an vim zril disputando voluptatibus, vix an salutandi sententiae.
                        </div>
                     </div>
                  </v-timeline-item>
               </v-timeline>
            </app-card>           
		   </v-layout>
	   </v-container>
   </div>
</template>

<script>

   export default {
      data: () => ({
         years: [
            {
               color: 'cyan',
               year: '1960'
            },
            {
               color: 'green',
               year: '1970'
            },
            {
               color: 'pink',
               year: '1980'
            },
            {
               color: 'amber',
               year: '1990'
            },
            {
               color: 'orange',
               year: '2000'
            }
         ]
      })
   }
</script>