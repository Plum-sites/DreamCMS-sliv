<template>
    <div>
        <v-container fluid grid-list-xl>
            <app-section-loader :status="!loaded"></app-section-loader>
            <notifications></notifications>

            <v-layout row wrap>
                <app-card
                        heading="Выбор игрока"
                        colClasses="xs12 sm6"
                        customClasses="mb-0 sales-widget"
                >
                    <user-selector v-model="selected.user"></user-selector>
                </app-card>

                <app-card
                        heading="Выбор группы"
                        colClasses="xs12 sm6"
                        customClasses="mb-0 sales-widget"
                >
                    <v-select item-value="id" v-model="selected.role" :items="roles" label="Выбрать роль сайта" item-text="name"></v-select>
                </app-card>
            </v-layout>

            <v-layout row wrap v-if="loaded && data">
                <app-card v-if="data.acluser"
                        heading="Пользователь"
                        colClasses="xs12 sm3"
                >
                    <div class="text-center">
                        <img class="profile-user-img img-fluid" :src="'/head/' + data.acluser.uuid + '/50'">
                    </div>

                    <h3 class="profile-username text-center">{{ data.acluser.login }}</h3>

                    <p class="text-muted text-center">ID: {{ data.acluser.id }}</p>
                </app-card>

                <app-card v-if="data.aclrole"
                        heading="Роль"
                        colClasses="xs12 sm3"
                >
                    <h3 class="profile-username text-center">{{ data.aclrole.name }}</h3>

                    <p class="text-muted text-center">ID: {{ data.aclrole.id }}</p>
                </app-card>

                <app-card v-if="data.aclrole || data.acluser"
                          heading="Не забудьте сохранить изменения!"
                          colClasses="xs12 sm3"
                >
                    <v-btn block color="error" @click="clear">Очистить права</v-btn>
                    <v-btn block color="success" @click="save">Сохранить</v-btn>
                </app-card>
            </v-layout>

            <v-layout row wrap v-if="loaded && data && data.common_map">
                <app-card
                        heading="Редактирование"
                        colClasses="xs12 sm12"
                >
                    <v-tabs grow>
                        <v-tab :key="category_key" v-for="(category, category_key) in data.common_map">{{ category_key }}</v-tab>
                        <v-tab>Доступ к ограниченным моделям</v-tab>


                        <v-tab-item :key="category_key" v-for="(category, category_key) in data.common_map">
                            <app-card :key="subcat_key" v-for="(subcat, subcat_key) in category" :heading="subcat.category" colClasses="xs12 sm12">
                                <table class="v-datatable v-table">
                                    <tbody>
                                        <tr>
                                            <th>Наименование</th>
                                            <th v-for="permission in subcat.permissions"></th>
                                        </tr>
                                        <tr v-if="subcat.child" v-for="(child, childkey) in subcat.child">
                                            <td>{{ child.label }}</td>
                                            <td v-for="(permission, permission_key) in subcat.permissions">
                                                <br>
                                                <v-tooltip top>
                                                    <v-checkbox @click.right.stop="copy(subcat_key + '.' + childkey + '.' + permission_key)" slot="activator" :disabled="child.permissions instanceof Array ? !child.permissions.includes(permission_key) : !child.permissions.hasOwnProperty(permission_key)" :label="permission" v-model="edit.permissions[subcat_key + '.' + childkey + '.' + permission_key]"></v-checkbox>
                                                    <span>{{ subcat_key + '.' + childkey + '.' + permission_key }}</span>
                                                </v-tooltip>
                                            </td>
                                        </tr>
                                        <tr v-if="!subcat.child">
                                            <td>Все</td>
                                            <td v-for="(permission, permission_key) in subcat.permissions">
                                                <br>
                                                <v-checkbox :label="permission" v-model="edit.admin_permissions[subcat_key][permission_key]"></v-checkbox>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </app-card>
                        </v-tab-item>

                        <v-tab-item>
                            <app-card :key="model_class" :heading="model.category" colClasses="xs12 sm12" v-for="(model, model_class) in data.models_map">
                                <table class="v-datatable v-table">
                                    <tbody>
                                    <tr>
                                        <th>Наименование</th>
                                        <th v-for="(permission, permission_key) in model.permissions"></th>
                                    </tr>
                                    <tr v-for="entity in model.entity">
                                        <td>{{ entity[model.label] }}</td>
                                        <td v-for="(permission, permission_key) in model.permissions">
                                            <br>
                                            <v-checkbox :label="permission" v-model="edit.model_permissions[model_class][permission_key][entity.id]"></v-checkbox>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </app-card>
                        </v-tab-item>
                    </v-tabs>
                </app-card>
            </v-layout>
        </v-container>
    </div>
</template>

<script>
    import api from 'Api';
    import UserSelector from '../components/UserSelector';

    export default {
        name: "ACL",

        components: { UserSelector },

        mounted() {
            this.loadData();
        },
        data() {
            return {
                loaded: false,

                selected: {
                    user: null,
                    role: null
                },

                roles: [],

                data: {
                    acluser: false,
                    aclrole: false,
                    common_map: false,
                    models_map: false
                },

                edit: {
                    permissions: {},
                    admin_permissions: {},
                    model_permissions: {},
                }
            }
        },

        methods:{
            clear(){

            },
            copy(str){
                alert(str);
            },
            save(){
                var arr = [];

                for(const [permission, value] of Object.entries(this.edit.permissions)){
                    if (value) arr.push({type: 'perm', perm: permission});
                }

                for(const [model_class, model] of Object.entries(this.edit.model_permissions)){
                    for(const [perm, permarr] of Object.entries(model)){
                        permarr.forEach((value, id) => {
                            if (value){
                                arr.push({type: 'byid', model: model_class, perm: perm, id: id});
                            }
                        });
                    }
                }

                for(const [model_class, model] of Object.entries(this.edit.admin_permissions)){
                    for(const [perm, value] of Object.entries(model)){
                        if (value){
                            arr.push({type: 'bymodel', model: model_class, perm: perm});
                        }
                    }
                }

                api.post("acl/save", {arr: arr, user: this.selected.user ? this.selected.user.id : null, role: this.selected.role})
                    .then(response => {
                        console.log(response);
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            loadData() {
                this.loaded = false;
                api.post("acl/load", {user: this.selected.user ? this.selected.user.id : null, role: this.selected.role})
                    .then(response => {
                        this.loaded = false;

                        this.edit.permissions = {};
                        this.edit.admin_permissions = {};
                        this.edit.model_permissions = {};

                        if(response.data.roles){
                            this.roles = response.data.roles;
                        }else {
                            this.data.acluser = response.data.acluser;
                            this.data.aclrole = response.data.aclrole;
                            this.data.common_map = response.data.common_map;
                            this.data.models_map = response.data.models_map;

                            for(const [category_key, category] of Object.entries(response.data.common_map)){
                                for(const [model_key, model] of Object.entries(category)){
                                    if (model.child){
                                        for(const [child_key, child] of Object.entries(model.child)){
                                            if(child.permissions instanceof Array){
                                                child.permissions.forEach((permission_key) => {
                                                    var key = model_key + '.' + child_key + '.' + permission_key;
                                                    this.$set(this.edit.permissions, key, response.data.perms.includes(key));
                                                });
                                            }else {
                                                for(const [permission_key, permission] of Object.entries(child.permissions)){
                                                    var key = model_key + '.' + child_key + '.' + permission_key;
                                                    this.$set(this.edit.permissions, key, response.data.perms.includes(key));
                                                }
                                            }
                                        }
                                    }else {
                                        this.$set(this.edit.admin_permissions, model_key, {});

                                        for(const [permission_key, permission] of Object.entries(model.permissions)){
                                            var has = false;

                                            try {
                                                has = response.data.models_perms[model_key]['global'] instanceof Array ? response.data.models_perms[model_key]['global'].includes(permission_key) : false;
                                            }catch (e) {}

                                            this.$set(this.edit.admin_permissions[model_key], permission_key, has);
                                        }
                                    }
                                }
                            }

                            for(const [model_class, model] of Object.entries(response.data.models_map)){
                                this.$set(this.edit.model_permissions, model_class, {});

                                for(const [permission_key, permission] of Object.entries(model.permissions)){
                                    this.$set(this.edit.model_permissions[model_class], permission_key, []);

                                    model.entity.forEach((entity) => {
                                        var has = false;

                                        try {
                                            has = response.data.models_perms[model_class][entity.id] instanceof Array ? response.data.models_perms[model_class][entity.id].includes(permission_key) : false;
                                        }catch (e) {}

                                        this.$set(this.edit.model_permissions[model_class][permission_key], entity.id, has);
                                    });
                                }
                            }
                        }

                        this.loaded = true;
                        console.log(response);
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
        },

        watch: {
            'selected.user': function (val){
                this.selected.role = null;
                this.loadData();
            },
            'selected.role': function (val){
                this.selected.user = null;
                this.loadData();
            }
        }
    }
</script>
