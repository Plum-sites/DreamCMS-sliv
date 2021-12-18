<template>
	<div>
		<page-title-bar></page-title-bar>
		<v-container fluid grid-list-xl pt-0>
			<v-layout row wrap>
				<app-card
					:heading="$t('message.usage')"
					colClasses="xs12 md12"
				>
					<div class="mb-3">
						<p class="mb-0">The <code>v-tabs</code> component is used for hiding content behind a selectable item. This can also be used as a pseudo-navigation for a page, where the tabs are links and the tab-items are the content.</p>
					</div>
					<v-tabs
						v-model="active"
						color="cyan"
						dark
						slider-color="yellow"
					>
						<v-tab
							v-for="item in items"
							:key="item"
							ripple
						>
							{{item}}

						</v-tab>
						<v-tab-item
							v-for="item in items"
							:key="item"
						>
							<v-card flat>
								<v-card-text>{{ text }}</v-card-text>
							</v-card>
						</v-tab-item>
					</v-tabs>

					<div class="text-xs-center mt-3">
						<v-btn @click="next">next tab</v-btn>
					</div>
				</app-card>
			</v-layout>
			<v-layout row wrap>
				<app-card
					:heading="$t('message.fixedTabs')"
					colClasses="xs12 md12"
				>
					<div class="mb-3">
						<p class="mb-0">The fixed-tabs prop sets a higher minimum width and applies a new maximum width to <code>v-tabs-items</code>. On desktop screens, the tab items will be centered within the <code>v-tabs</code> component and switch to evenly fill on mobile.</p>
					</div>
					<v-tabs fixed-tabs>
						<v-tab
							v-for="item in items"
							:key="item"
						>
							{{item}}
						</v-tab>
					</v-tabs>
				</app-card>
			</v-layout>
			<v-layout>
				<app-card
					:heading="$t('message.rightAlignedTabs')"
					colClasses="xs12 md12"
				>
					<div class="mb-3">
						<p class="mb-0">The right prop aligns the tabs to the right.</p>
					</div>
					<v-tabs right>
						<v-tab
							v-for="item in items"
							:key="item"
						>
							{{item}}
						</v-tab>
					</v-tabs>
				</app-card>
			</v-layout>
			<v-layout>
				<app-card
					:heading="$t('message.content')"
					colClasses="xs12 md12"
				>
					<div class="mb-3">
						<p class="mb-0">Tabs are not the only thing you can put inside the <code>v-tabs</code> component. In this example we've also added a toolbar.</p>
					</div>
					<v-toolbar
						color="cyan"
						dark
						tabs
					>
						<v-toolbar-side-icon></v-toolbar-side-icon>

						<v-toolbar-title>Page title</v-toolbar-title>

						<v-spacer></v-spacer>

						<v-btn icon>
							<v-icon>search</v-icon>
						</v-btn>

						<v-btn icon>
							<v-icon>more_vert</v-icon>
						</v-btn>

						<v-tabs
							slot="extension"
							v-model="model"
							centered
							color="cyan"
							slider-color="yellow"
						>
						<v-tab
							v-for="i in 3"
							:key="i"
							:href="`#tab-${i}`"
						>
							Item {{ i }}
						</v-tab>
						</v-tabs>
					</v-toolbar>

					<v-tabs-items v-model="model">
						<v-tab-item
						v-for="i in 3"
						:value="`tab-${i}`"
						:key="i"
						>
						<v-card flat>
							<v-card-text v-text="text"></v-card-text>
						</v-card>
						</v-tab-item>
					</v-tabs-items>
				</app-card>
			</v-layout>
			<v-layout>
				<app-card
					:heading="$t('message.withSearch')"
					colClasses="xs12 md12"
				>
					<div class="mb-3">
						<p class="mb-0">Here is another example of additional content inside the <code>v-tabs component</code>.</p>
					</div>
					<v-toolbar
			      color="purple"
			      dark
			      tabs
			    >
			      <v-text-field
			        append-icon="mic"
			        class="mx-3"
			        flat
			        label="Search"
			        prepend-inner-icon="search"
			        solo-inverted
			      ></v-text-field>

			      <v-tabs
			        slot="extension"
			        v-model="tabs"
			        centered
			        color="transparent"
			        slider-color="white"
			      >
			        <v-tab
			          v-for="item in items"
			          :key="item"
			        >
			          {{item}}
			        </v-tab>
			      </v-tabs>
			    </v-toolbar>

			    <v-tabs-items v-model="tabs">
			      <v-tab-item
			        v-for="item in items"
			        :key="item"
			      >
			        <v-card>
			          <v-card-text>
			            {{ text }}
			          </v-card-text>
			        </v-card>
			      </v-tab-item>
			    </v-tabs-items>
				</app-card>
			</v-layout>
			<v-layout>
				<app-card
					:heading="$t('message.iconsAndText')"
					colClasses="xs12 md12"
				>
					<div class="mb-3">
						<p class="mb-0">By using the icons-and-text prop you can add icons to each tab item.</p>
					</div>
					<v-tabs
				    centered
				    color="cyan"
				    dark
				    icons-and-text
				  >
				    <v-tabs-slider color="yellow"></v-tabs-slider>

				    <v-tab v-for="(icon, index) in tabIcons" :href="'#tab-'+index" :key="index">
				      {{icon.text}}
				      <v-icon>{{icon.icon}}</v-icon>
				    </v-tab>

				    <v-tab-item
				      v-for="(icon,index) in tabIcons"
				      :value="'tab-' + index"
				      :key="index"
				    >
				      <v-card flat>
				        <v-card-text>{{ text }}</v-card-text>
				      </v-card>
				    </v-tab-item>
				  </v-tabs>
				</app-card>
			</v-layout>
			<v-layout>
				<app-card
					:heading="$t('message.grow')"
					colClasses="xs12 md12"
				>
					<div class="mb-3">
						<p class="mb-0">The grow prop will make the tab items take up all available space.</p>
					</div>
					<v-toolbar
			      color="cyan"
			      dark
			      tabs
			    >
			      <v-toolbar-side-icon></v-toolbar-side-icon>

			      <v-toolbar-title>Page title</v-toolbar-title>

			      <v-spacer></v-spacer>

			      <v-btn icon>
			        <v-icon>search</v-icon>
			      </v-btn>

			      <v-btn icon>
			        <v-icon>more_vert</v-icon>
			      </v-btn>

			      <v-tabs
			        slot="extension"
			        v-model="tabs"
			        color="cyan"
			        grow
			      >
			        <v-tabs-slider color="yellow"></v-tabs-slider>

			        <v-tab
			          v-for="item in items"
			          :key="item"
			        >
			          {{ item }}
			        </v-tab>
			      </v-tabs>
			    </v-toolbar>

			    <v-tabs-items v-model="tabs">
			      <v-tab-item
			        v-for="item in items"
			        :key="item"
			      >
			        <v-card flat>
			          <v-card-text>{{ text }}</v-card-text>
			        </v-card>
			      </v-tab-item>
			    </v-tabs-items>
				</app-card>
			</v-layout>
			<v-layout>
				<app-card
					:heading="$t('message.pagination')"
					colClasses="xs12 md12"
				>
					<div class="mb-3">
						<p class="mb-0">You can use a menu to hold additional tabs, swapping them out on the fly.</p>
					</div>
					<v-tabs
				    dark
				    color="cyan"
				    show-arrows
				  >
				    <v-tabs-slider color="yellow"></v-tabs-slider>

				    <v-tab
				      v-for="i in 12"
				      :href="'#tab-' + i"
				      :key="i"
				    >
				      Item {{ i }}
				    </v-tab>

				    <v-tabs-items>
				      <v-tab-item
				        v-for="i in 12"
				        :value="'tab-' + i"
				        :key="i"
				      >
				        <v-card flat>
				          <v-card-text>{{ text }}</v-card-text>
				        </v-card>
				      </v-tab-item>
				    </v-tabs-items>
				  </v-tabs>
				</app-card>
			</v-layout>
			<v-layout>
				<app-card
					:heading="$t('message.customIcons')"
					colClasses="xs12 md12"
				>
					<div class="mb-3">
						<p class="mb-0">You can use a menu to hold additional tabs, swapping them out on the fly.</p>
					</div>
					<v-tabs
				    color="cyan"
				    dark
				    next-icon="mdi-arrow-right-bold-box-outline"
				    prev-icon="mdi-arrow-left-bold-box-outline"
				    show-arrows
				  >
				    <v-tabs-slider color="yellow"></v-tabs-slider>
				    <v-tab
				      v-for="i in 12"
				      :href="'#tab-' + i"
				      :key="i"
				    >
				      Item {{ i }}
				    </v-tab>

				    <v-tabs-items>
				      <v-tab-item
				        v-for="i in 30"
				        :value="'tab-' + i"
				        :key="i"
				      >
				        <v-card flat>
				          <v-card-text>{{ text }}</v-card-text>
				        </v-card>
				      </v-tab-item>
				    </v-tabs-items>
				  </v-tabs>
				</app-card>
			</v-layout>
		</v-container>
	</div>
</template>

<script>
export default {
  data() {
    return {
			tabIcons: [
				{
					icon: "phone",
					text: "Recents"
				},
				{
					icon: "favorite",
					text: "Favorites"
				},
				{
					icon: "account_box",
					text: "nearby"
				}
			],
			model: 'tab-2',
			tabs: null,
      active: null,
      items: ["Item One", "Item Five", "Item Seventeen"],
      text:
        "Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat."
    };
  },
  methods: {
  	next () {
			const active = parseInt(this.active)
			this.active = (active < 2 ? active + 1 : 0)
		}
  }
};
</script>
