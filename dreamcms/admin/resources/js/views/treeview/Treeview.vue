<template>
	<div class="hover-wrapper">
		<page-title-bar></page-title-bar>
		<v-container grid-list-xl pt-0>
			<v-layout row wrap>
				<app-card
					:heading="$t('message.usage')"
					customClasses="mb-20"
					colClasses="xl12 lg12 md12 sm12 xs12"
				>
					<div class="mb-4">
						<p>The <code>v-treeview</code> component is useful for displaying large amounts of nested data.</p>
					</div>
					<template>
                  <v-treeview :items="items"></v-treeview>
               </template>
				</app-card>
        		<app-card
					:heading="$t('message.scopedSlots')"
					customClasses="mb-20"
					colClasses="xl12 lg12 md12 sm12 xs12"
				>
					<div class="mb-4">
						<p>Using scoped slots we are able to create an intuitive file explorer</p>
					</div>
					<v-treeview
						v-model="tree"
						:open="open"
						:items="slotsItems"
						activatable
						item-key="name"
						open-on-click
					>
						<template slot="prepend" slot-scope="{ item, open, leaf }">
							<v-icon v-if="!item.file">
								{{ open ? 'mdi-folder-open' : 'mdi-folder' }}
							</v-icon>
							<v-icon v-else>
								{{ files[item.file] }}
							</v-icon>
						</template>
					</v-treeview>
				</app-card>
				<app-card
					:heading="$t('message.asyncItems')"
					customClasses="mb-20"
					colClasses="xl12 lg12 md12 sm12 xs12"
				>
					<div class="mb-4">
						<p>You can dynamically load child data by supplying a Promise callback to the <code>load-children</code> prop. This callback will be executed the first time a user tries to expand an item that has a children property that is an empty array.</p>
					</div>
					<v-card>
						<v-card-title class="indigo white--text headline">
							User Directory
						</v-card-title>
						<v-layout
							justify-space-between
							pa-3
						>
							<v-flex xs5>
								<v-treeview
									:active.sync="active"
									:items="asyncItems"
									:load-children="fetchUsers"
									:open.sync="asyncItemsOpen"
									activatable
									active-class="primary--text"
									class="grey lighten-5"
									open-on-click
									transition
								>
									<v-icon
										v-if="!item.children"
										slot="prepend"
										slot-scope="{ item, active }"
										:color="active ? 'primary' : ''"
										>mdi-account
									</v-icon>
								</v-treeview>
							</v-flex>
							<v-flex
								d-flex
								text-xs-center
							>
								<v-scroll-y-transition mode="out-in">
									<div
										v-if="!selected"
										class="title grey--text text--lighten-1 font-weight-light"
										style="align-self: center;"
									>
										Select a User
									</div>
									<v-card
										v-else
										:key="selected.id"
										class="pt-4 mx-auto"
										flat
										max-width="400"
									>
										<v-card-text>
											<v-avatar
												v-if="avatar"
												size="88"
											>
												<v-img
													:src="`https://avataaars.io/${avatar}`"
													class="mb-4"
												></v-img>
											</v-avatar>
											<h3 class="headline mb-2">
												{{ selected.name }}
											</h3>
											<div class="blue--text mb-2">{{ selected.email }}</div>
											<div class="blue--text subheading font-weight-bold">{{ selected.username }}</div>
										</v-card-text>
										<v-divider></v-divider>
										<v-layout
											tag="v-card-text"
											text-xs-left
											wrap
										>
											<v-flex tag="strong" xs5 text-xs-right mr-3 mb-2>Company:</v-flex>
											<v-flex>{{ selected.company.name }}</v-flex>
											<v-flex tag="strong" xs5 text-xs-right mr-3 mb-2>Website:</v-flex>
											<v-flex>
												<a :href="`//${selected.website}`" target="_blank">{{ selected.website }}</a>
											</v-flex>
											<v-flex tag="strong" xs5 text-xs-right mr-3 mb-2>Phone:</v-flex>
											<v-flex>{{ selected.phone }}</v-flex>
										</v-layout>
									</v-card>
								</v-scroll-y-transition>
							</v-flex>
						</v-layout>
					</v-card>
				</app-card>
				<app-card
					:heading="$t('message.customSelectableIcons')"
					customClasses="mb-20"
					colClasses="xl12 lg12 md12 sm12 xs12"
				>
					<div class="mb-4">
						<p>Customize the on, off and indeterminate icons for your selectable tree. Combine with other advanced functionality like API loaded items.</p>
					</div>
					<v-card>
						<v-toolbar
							card
							color="grey lighten-3"
						>
							<v-icon>mdi-silverware</v-icon>
							<v-toolbar-title>Local hotspots</v-toolbar-title>
						</v-toolbar>

						<v-layout>
							<v-flex>
								<v-card-text>
									<v-treeview
										v-model="customTree"
										:load-children="fetched"
										:items="selectableIcons"
										activatable
										active-class="grey lighten-4 indigo--text"
										selected-color="indigo"
										open-on-click
										selectable
										expand-icon="mdi-chevron-down"
										on-icon="mdi-bookmark"
										off-icon="mdi-bookmark-outline"
										indeterminate-icon="mdi-bookmark-minus"
									>
									</v-treeview>
								</v-card-text>
							</v-flex>
							<v-divider vertical></v-divider>

							<v-flex
								xs12
								md6
							>
								<v-card-text>
									<div
										v-if="selections.length === 0"
										key="title"
										class="title font-weight-light grey--text pa-3 text-xs-center"
									>
										Select your favorite breweries
									</div>
									<v-scroll-x-transition
										group
										hide-on-leave
									>
										<v-chip
											v-for="(selection, i) in selections"
											:key="i"
											color="grey"
											dark
											small
										>
											<v-icon left small>mdi-beer</v-icon>
											{{ selection.name }}
										</v-chip>
									</v-scroll-x-transition>
								</v-card-text>
							</v-flex>
						</v-layout>
						<v-divider></v-divider>

						<v-card-actions>
							<v-btn
								flat
								@click="customTree = []"
							>
								Reset
							</v-btn>
							<v-spacer></v-spacer>
							<v-btn
								class="white--text"
								color="green darken-1"
								depressed
							>
								Save
								<v-icon right>mdi-content-save</v-icon>
							</v-btn>
						</v-card-actions>
					</v-card>
        		</app-card>
			</v-layout>
		</v-container>
   </div>
</template>

<script>
	const avatars = [
   	'?accessoriesType=Blank&avatarStyle=Circle&clotheColor=PastelGreen&clotheType=ShirtScoopNeck&eyeType=Wink&eyebrowType=UnibrowNatural&facialHairColor=Black&facialHairType=MoustacheMagnum&hairColor=Platinum&mouthType=Concerned&skinColor=Tanned&topType=Turban',
    	'?accessoriesType=Sunglasses&avatarStyle=Circle&clotheColor=Gray02&clotheType=ShirtScoopNeck&eyeType=EyeRoll&eyebrowType=RaisedExcited&facialHairColor=Red&facialHairType=BeardMagestic&hairColor=Red&hatColor=White&mouthType=Twinkle&skinColor=DarkBrown&topType=LongHairBun',
    	'?accessoriesType=Prescription02&avatarStyle=Circle&clotheColor=Black&clotheType=ShirtVNeck&eyeType=Surprised&eyebrowType=Angry&facialHairColor=Blonde&facialHairType=Blank&hairColor=Blonde&hatColor=PastelOrange&mouthType=Smile&skinColor=Black&topType=LongHairNotTooLong',
    	'?accessoriesType=Round&avatarStyle=Circle&clotheColor=PastelOrange&clotheType=Overall&eyeType=Close&eyebrowType=AngryNatural&facialHairColor=Blonde&facialHairType=Blank&graphicType=Pizza&hairColor=Black&hatColor=PastelBlue&mouthType=Serious&skinColor=Light&topType=LongHairBigHair',
    	'?accessoriesType=Kurt&avatarStyle=Circle&clotheColor=Gray01&clotheType=BlazerShirt&eyeType=Surprised&eyebrowType=Default&facialHairColor=Red&facialHairType=Blank&graphicType=Selena&hairColor=Red&hatColor=Blue02&mouthType=Twinkle&skinColor=Pale&topType=LongHairCurly'
  	]

  const pause = ms => new Promise(resolve => setTimeout(resolve, ms))
  export default {
    data: () => ({
      items: [
        {
          id: 1,
          name: 'Applications :',
          children: [
            { id: 2, name: 'Calendar : app' },
            { id: 3, name: 'Chrome : app' },
            { id: 4, name: 'Webstorm : app' }
          ]
        },
        {
          id: 5,
          name: 'Documents :',
          children: [
            {
              id: 6,
              name: 'vuetify :',
              children: [
                {
                  id: 7,
                  name: 'src :',
                  children: [
                    { id: 8, name: 'index : ts' },
                    { id: 9, name: 'bootstrap : ts' }
                  ]
                }
              ]
            },
            {
              id: 10,
              name: 'material2 :',
              children: [
                {
                  id: 11,
                  name: 'src :',
                  children: [
                    { id: 12, name: 'v-btn : ts' },
                    { id: 13, name: 'v-card : ts' },
                    { id: 14, name: 'v-window : ts' }
                  ]
                }
              ]
            }
          ]
        },
        {
          id: 15,
          name: 'Downloads :',
          children: [
            { id: 16, name: 'October : pdf' },
            { id: 17, name: 'November : pdf' },
            { id: 18, name: 'Tutorial : html' }
          ]
        },
        {
          id: 19,
          name: 'Videos :',
          children: [
            {
              id: 20,
              name: 'Tutorials :',
              children: [
                { id: 21, name: 'Basic layouts : mp4' },
                { id: 22, name: 'Advanced techniques : mp4' },
                { id: 23, name: 'All about app : dir' }
              ]
            },
            { id: 24, name: 'Intro : mov' },
            { id: 25, name: 'Conference introduction : avi' }
          ]
        }
      ],
      open: ['public'],
      files: {
        html: 'mdi-language-html5',
        js: 'mdi-nodejs',
        json: 'mdi-json',
        md: 'mdi-markdown',
        pdf: 'mdi-file-pdf',
        png: 'mdi-file-image',
        txt: 'mdi-file-document-outline',
        xls: 'mdi-file-excel'
      },
      tree: [],
      slotsItems: [
        {
          name: '.git'
        },
        {
          name: 'node_modules'
        },
        {
          name: 'public',
          children: [
            {
              name: 'static',
              children: [{
                name: 'logo.png',
                file: 'png'
              }]
            },
            {
              name: 'favicon.ico',
              file: 'png'
            },
            {
              name: 'index.html',
              file: 'html'
            }
          ]
        },
        {
          name: '.gitignore',
          file: 'txt'
        },
        {
          name: 'babel.config.js',
          file: 'js'
        },
        {
          name: 'package.json',
          file: 'json'
        },
        {
          name: 'README.md',
          file: 'md'
        },
        {
          name: 'vue.config.js',
          file: 'js'
        }
      ],
      active: [],
      avatar: null,
      asyncItemsOpen: [],
      users: [],
      breweries: [],
      isLoading: false,
      customTree: [],
      types: []
    }),
    computed: {
      asyncItems () {
        return [
          {
            name: 'Users',
            children: this.users
          }
        ]
      },
      selected () {
        if (!this.active.length) return undefined
        const id = this.active[0]
        return this.users.find(user => user.id === id)
      },
      selectableIcons () {
      	const children = this.types.map(type => ({
        		id: type,
          	name: this.getName(type),
          	children: this.getChildren(type)
      	}))
        	return [{
        		id: 1,
          	name: 'All Breweries',
          	children
       	}]
      },
      selections () {
      	const selections = []
        	for (const leaf of this.customTree) {
        		const brewery = this.breweries.find(brewery => brewery.id === leaf)
				if (!brewery) continue
          	selections.push(brewery)
        	}
        	return selections
      },
      shouldShowTree () {
        return this.breweries.length > 0 && !this.isLoading
      }
    },

    watch: {
    	selected: 'randomAvatar',
      breweries (val) {
      	this.types = val.reduce((acc, cur) => {
         	const type = cur.brewery_type
          	if (!acc.includes(type)) acc.push(type)
				return acc
        	}, []).sort()
      }
    },

    methods: {
    	async fetchUsers (item) {
      	// Remove in 6 months and say
        	// you've made optimizations! :)
        	await pause(1500)

        	return fetch('https://jsonplaceholder.typicode.com/users')
			.then(res => res.json())
			.then(json => (item.children.push(...json)))
			.catch(err => console.warn(err))
		},
		randomAvatar () {
			this.avatar = avatars[Math.floor(Math.random() * avatars.length)]
		},
      fetched () {
        if (this.breweries.length) return
        return fetch('https://api.openbrewerydb.org/breweries')
          .then(res => res.json())
          .then(data => (this.breweries = data))
          .catch(err => console.log(err))
      },
      getChildren (type) {
      	const breweries = []
        	for (const brewery of this.breweries) {
         	if (brewery.brewery_type !== type) continue
				breweries.push({
					...brewery,
					name: this.getName(brewery.name)
				})
        	}
			return breweries.sort((a, b) => {
				return a.name > b.name ? 1 : -1
			})
      },
      getName (name) {
        return `${name.charAt(0).toUpperCase()}${name.slice(1)}`
      }
    }
  }
</script>