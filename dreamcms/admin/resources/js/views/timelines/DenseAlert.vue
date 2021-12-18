<template>
	<div class="hover-wrapper">
		<page-title-bar></page-title-bar>
		<v-container grid-list-xl pt-0>
			<v-layout row wrap>				
            <app-card
               colClasses="xl12 lg12 md12 sm12 xs12"
            >
               <div class="mb-4">
                  <p>Dense timelines position all content to the right. In this example, <code>v-alert</code> replaces the card to provide a different design.</p>
               </div>
               <v-card
                  class="mx-auto"
                  max-width="600"
               >
                  <v-card-title
                     class="blue-grey white--text"
                  >
                     <span class="title">Logs</span>
                     <v-spacer></v-spacer>
                     <v-btn
                        :outline="interval == null"
                        :color="interval == null ? 'white' : 'primary'"
                        depressed
                        @click="interval == null ? start() : stop()"
                     >
                        Realtime Logging
                     </v-btn>
                  </v-card-title>
                  <v-card-text class="py-0">
                     <v-timeline dense>
                        <v-slide-x-reverse-transition
                           group
                           hide-on-leave
                        >
                           <v-timeline-item
                              v-for="item in denseItems"
                              :key="item.id"
                              :color="item.color"
                              small
                              fill-dot
                           >
                              <v-alert
                                 :value="true"
                                 :color="item.color"
                                 :icon="item.icon"
                              >
                                 Lorem ipsum dolor sit amet, no nam oblique veritus. Commune scaevola imperdiet nec ut, sed euismod convenire principes at. Est et nobis iisque percipit, an vim zril disputando voluptatibus, vix an salutandi sententiae.
                              </v-alert>
                           </v-timeline-item>
                        </v-slide-x-reverse-transition>
                     </v-timeline>
                  </v-card-text>
               </v-card>
            </app-card>           
		   </v-layout>
	   </v-container>
   </div>
</template>

<script>
   const COLORS = [
    'info',
    'warning',
    'error',
    'success'
  ]
  const ICONS = {
    info: 'mdi-information',
    warning: 'mdi-alert',
    error: 'mdi-alert-circle',
    success: 'mdi-check-circle'
  }

   export default {
      data: () => ({
         interval: null,
         denseItems: [
            {
               id: 1,
               color: 'info',
               icon: ICONS['info']
            }
         ],
         nonce: 2
      }),
      beforeDestroy () {
         this.stop()
      },
      methods: {
         addEvent () {
            let { color, icon } = this.genAlert()

            const previousColor = this.denseItems[0].color

            while (previousColor === color) {
               color = this.genColor()
            }

            this.denseItems.unshift({
               id: this.nonce++,
               color,
               icon
            })

            if (this.nonce > 6) {
               this.denseItems.pop()
            }
         },
         genAlert () {
            const color = this.genColor()
            return {
               color,
               icon: this.genIcon(color)
            }
         },
         genColor () {
            return COLORS[Math.floor(Math.random() * 3)]
         },
         genIcon (color) {
            return ICONS[color]
         },
         start () {
            this.interval = setInterval(this.addEvent, 3000)
         },
         stop () {
            clearInterval(this.interval)
            this.interval = null
         }
      }
   }
</script>