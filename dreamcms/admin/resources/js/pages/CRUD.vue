<template>
    <div>
        <app-section-loader :status="!loaded"></app-section-loader>
        <notifications></notifications>
        <v-container fluid grid-list-xl v-if="loaded">
            <v-layout row wrap>
                <app-card
                        heading="Поиск по записям"
                        :fullBlock="true"
                        colClasses="xl12 lg12 md12 sm12 xs12"
                >

                    <v-card-title>
                        {{ crud.entity_name_plural }}
                        <v-spacer></v-spacer>
                        <v-text-field
                                append-icon="search"
                                label="Поиск"
                                single-line
                                hide-details
                                v-model="search"
                        >
                        </v-text-field>
                        <v-spacer></v-spacer>
                        <v-btn @click="openCreate">Создать запись</v-btn>
                    </v-card-title>

                    <v-data-table
                            v-bind:headers="headers"
                            v-bind:items="items"
                            hide-actions
                    >
                        <template slot="items" slot-scope="props">
                            <!--<td>
                                <v-checkbox
                                        color="primary"
                                        hide-details
                                        :input-value="props.selected"
                                ></v-checkbox>
                            </td>-->

                            <td v-for="key in Object.keys(props.item)" v-if="key !== 'id'">
                                <span v-html="props.item[key]"></span>
                            </td>

                            <td>
                                <v-btn icon class="mx-0" @click="editCRUD(props.item.id)" >
                                    <v-icon color="grey lighten-1" >edit</v-icon>
                                </v-btn>
                                <v-btn icon class="mx-0" @click="deleteCRUD(props.item.id)" >
                                    <v-icon color="grey lighten-1" >close</v-icon>
                                </v-btn>
                            </td>

                        </template>

                        <template slot="footer">
                            <v-flex>
                                <v-btn icon @click="options.page--">
                                    <v-icon color="grey lighten-1" >navigate_before</v-icon>
                                </v-btn>
                                <span>Страница {{ options.page }} / {{ maxPages }}</span>
                                <v-btn icon @click="options.page++">
                                    <v-icon color="grey lighten-1" >navigate_next</v-icon>
                                </v-btn>
                            </v-flex>
                        </template>
                    </v-data-table>
                </app-card>
            </v-layout>
        </v-container>

        <v-dialog v-model="dialogs.create" transition="dialog-bottom-transition" overlay=false scrollable>
            <v-card>
                <v-toolbar color="primary" dark>
                    <v-btn icon @click.native="dialogs.create = false" dark>
                        <v-icon>close</v-icon>
                    </v-btn>
                    <v-toolbar-title>Создание записи</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-toolbar-items>
                        <v-btn dark flat @click.native="save">Сохранить</v-btn>
                    </v-toolbar-items>
                </v-toolbar>

                <v-card-text>
                    <v-form>
                        <c-r-u-d-field :field="field" v-for="field in crud.create_fields" :value="create[field.name]" v-model="create[field.name]" v-bind:key="field.tkey"></c-r-u-d-field>
                    </v-form>
                </v-card-text>
                <div style="flex: 1 1 auto;"></div>
            </v-card>
        </v-dialog>

        <v-dialog v-model="dialogs.edit" transition="dialog-bottom-transition" overlay=false scrollable>
            <v-card>
                <v-toolbar color="primary" dark>
                    <v-btn icon @click.native="dialogs.edit = false" dark>
                        <v-icon>close</v-icon>
                    </v-btn>
                    <v-toolbar-title>Редактирование записи</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-toolbar-items>
                        <v-btn dark flat @click.native="saveEdit(edit.entry.id)">Сохранить</v-btn>
                    </v-toolbar-items>
                </v-toolbar>

                <v-card-text>
                    <v-form v-model="edit_form">
                        <c-r-u-d-field :field="field" v-for="field in edit.fields" :value="edit.entry[field.name]" v-model="edit.entry[field.name]" v-bind:key="field.tkey"></c-r-u-d-field>
                    </v-form>
                </v-card-text>
                <div style="flex: 1 1 auto;"></div>
            </v-card>
        </v-dialog>

        <v-dialog v-model="dialogs.delete" transition="dialog-bottom-transition" overlay=false scrollable>
            <v-card>
                <v-toolbar color="primary" dark>
                    <v-btn icon @click.native="dialogs.delete = false" dark>
                        <v-icon>close</v-icon>
                    </v-btn>
                    <v-toolbar-title>Удаление записи</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-toolbar-items>
                        <v-btn dark flat @click.native="deleteCRUD(delete_id, true)">Удалить</v-btn>
                    </v-toolbar-items>
                </v-toolbar>
                <div style="flex: 1 1 auto;"></div>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>
    import api from "Api";
    import moment from "moment";

    import CRUDField from "../components/CRUDField";
    import _ from "lodash";

    export default {
        components:{
            CRUDField,
        },
        data() {
            return {
                loaded: false,

                tmp: "",
                search: "",

                crud: {},

                headers: [],

                items: [],

                totalItems: 0,

                options: {
                    sortBy: "",
                    descending: false,
                    page: 1,
                    itemsPerPage: 10
                },

                dialogs:{
                    create: false,
                    edit: false,
                    delete: false,
                    relation: false,
                },

                edit: {},
                create: {},
                create_entries: {},
                edit_form: false,

                delete_id: 0
            };
        },
        computed: {
            startFrom(){
                return this.options.page > 1 ? (this.options.page - 1) * this.options.itemsPerPage : 0;
            },
            maxPages(){
                return Math.ceil(this.totalItems / this.options.itemsPerPage);
            }
        },
        mounted() {
            this.loadCRUD();
        },
        methods: {
            loadData() {
                this.loaded = false;
                api
                    .post("crud/" + this.$route.params.model + "/search", {
                        length: this.options.itemsPerPage,
                        search: {
                            value: this.search
                        },
                        start: this.startFrom
                    })
                    .then(response => {
                        this.items = response.data.data;
                        this.totalItems = response.data.recordsFiltered;
                        this.loaded = true;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            loadCRUD() {
                api
                    .get("crud/" + this.$route.params.model)
                    .then(response => {
                        this.crud = response.data.crud;

                        this.crud.create_fields = response.data.create_fields;

                        for(const [key, item] of Object.entries(response.data.columns)){
                            this.headers.push({
                                text: item.label,
                                align: "left",
                                sortable: item.orderable,
                                value: item.key
                            });
                        }

                        for(const [key, item] of Object.entries(this.crud.create_fields)){
                            this.$set(this.create, key, null);
                        }

                        this.headers.push({
                            text: "Действия",
                            align: "right",
                            sortable: false,
                            value: "actions"
                        });

                        this.loadData();
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            openCreate(){
                this.dialogs.create = true;
            },
            save(){
                api.post("crud/" + this.$route.params.model, this.create)
                    .then(response => {
                        if (response.data.success){
                            this.$notify({
                                type: 'success',
                                title: 'Успешно',
                                text: 'Вы успешно создали запись!'
                            });

                            this.dialogs.create = false;
                        }else {
                            this.$notify({
                                type: 'error',
                                title: 'Ошибка',
                                text: 'Проверьте правильность ввода данных!'
                            });
                        }
                    })
                    .catch(error => {
                        this.$notify({
                            type: 'error',
                            title: 'Ошибка',
                            text: 'Ошибка при выполнении запроса!'
                        });
                        console.log(error);
                    });
            },
            editCRUD(id){
                api
                    .get("crud/" + this.$route.params.model + "/" + id + "/edit")
                    .then(response => {
                        this.edit = response.data;

                        for(const [key, field] of Object.entries(this.edit.fields)){
                            if (field.type === 'select_multiple' || field.type === 'checklist' || field.type === 'select2_multiple'){
                                var arr = [];

                                for(const [key2, value] of Object.entries( this.edit.fields[key].value)){
                                  if (value != null){
                                      if (value.id){
                                        arr.push(value.id);
                                      }else {
                                        arr.push(value);
                                      }
                                  }
                                }
                                this.edit.entry[field.name] = arr;
                            }
                            if (field.type === 'date'){
                                this.edit.entry[field.name] = moment(this.edit.entry[field.name]);
                            }
                            if (field.type === 'date_range'){
                                this.edit.entry[field.name] = {
                                    start: moment(this.edit.entry[field.start_name]),
                                    end: moment(this.edit.entry[field.end_name]),
                                };
                            }
                        }

                        this.dialogs.edit = true;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            deleteCRUD(id, confirm = false){
                if (confirm){
                    api.delete("crud/" + this.$route.params.model + '/' + id).then(response => {
                        if (response.data.success|| response.data.data.success){
                            this.$notify({
                                type: 'success',
                                title: 'Успешно',
                                text: 'Вы успешно удалили запись!'
                            });

                            this.dialogs.delete = false;
                        }else {
                            this.$notify({
                                type: 'error',
                                title: 'Ошибка',
                                text: 'Проверьте правильность ввода данных!'
                            });
                        }
                    })
                    .catch(error => {
                        this.$notify({
                            type: 'error',
                            title: 'Ошибка',
                            text: 'Ошибка при выполнении запроса!'
                        });
                        console.log(error);
                    });
                }else {
                    this.delete_id = id;
                    this.dialogs.delete = true;
                }
            },
            saveEdit(id){
                var form = { id: id };

                for(const [key, field] of Object.entries(this.edit.fields)){
                    if (field.type === 'date'){
                        form[field.name] = this.edit.entry[field.name].format("YYYY-MM-DD");
                    }
                    else if (field.type === 'date_range'){
                        form[field.start_name] = this.edit.entry[field.name].start.format("YYYY-MM-DD HH:mm:ss");
                        form[field.end_name] = this.edit.entry[field.name].end.format("YYYY-MM-DD HH:mm:ss");
                    }
                    else {
                        form[field.name] = this.edit.entry[field.name];
                    }
                }

                api.put("crud/" + this.$route.params.model + "/" + id, form)
                    .then(response => {
                        if (response.data.success){
                            this.$notify({
                                type: 'success',
                                title: 'Успешно',
                                text: 'Сохранено!'
                            });
                        }
                    });
            },
            runSearch: _.debounce((vm) => {
                vm.loadData();
            }, 1000),
        },
        watch: {
            '$route' (to, from) {
                this.loaded = false;
                this.options.sortBy = "";
                this.options.descending = false;
                this.options.page = 1;

                this.headers = [];
                this.loadCRUD();
            },
            search: function (val) {
                this.options.sortBy = "";
                this.options.descending = false;
                this.options.page = 1;

                this.runSearch(this);
            },
            'options.page': function (val) {
                if (val < 0){
                    this.options.page = 1;
                }
                if (val > this.maxPages){
                    this.options.page = val - 1;
                }
                this.loadData();
            }
        }
    };
</script>
