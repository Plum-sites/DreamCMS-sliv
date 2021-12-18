<template>
	<div class="hover-wrapper">
		<page-title-bar></page-title-bar>
		<v-container grid-list-xl pt-0>
			<v-layout row wrap>
            <app-card
					colClasses="xl12 lg12 md12 sm12 xs12"
				>
               <div class="mb-4">
						<p>Conditionally use icons within the <code>v-timeline-item</code> 's dot to provide additional context.</p>
					</div>
               <v-timeline align-top>
                  <v-timeline-item
                     v-for="(item, i) in items"
                     :color="item.color"
                     :icon="item.icon"
                     :key="i"
                     class="white--text "
                     fill-dot
                  >
                     <v-card
                        :color="item.color"
                     >
                        <v-card-title class="title white--text">Lorem Ipsum Dolor</v-card-title>
                        <v-card-text class="white text--primary">
                           <p>Lorem ipsum dolor sit amet, no nam oblique veritus. Commune scaevola imperdiet nec ut, sed euismod convenire principes at. Est et nobis iisque percipit, an vim zril disputando voluptatibus, vix an salutandi sententiae.</p>
                           <v-btn
                              :color="item.color"
                              class="mx-0"
                              outline
                           >
                              Button
                           </v-btn>
                        </v-card-text>
                     </v-card>
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
         items: [
            {
               color: 'red lighten-2',
               icon: 'mdi-star'
            },
            {
               color: 'purple darken-1',
               icon: 'mdi-book-variant'
            },
            {
               color: 'green lighten-1',
               icon: 'mdi-airballoon'
            },
            {
               color: 'indigo',
               icon: 'mdi-buffer'
            }
         ]
      })
   }
</script>