<template>
    <div>
      <b-card v-if="loaded"
          no-body
          class="mb-0"
      >

        <div class="m-2">
          <b-row>
            <b-col
                cols="12"
                sm="12"
            >
              <div class="d-flex align-items-center justify-content-end">
                <b-form-input
                    v-model="search"
                    class="d-inline-block mr-1"
                    placeholder="Поиск..."
                />
                <b-button
                    variant="primary"
                    @click="openCreate"
                >
                  <span class="text-nowrap">Создать {{ crud.entity_name }}</span>
                </b-button>
              </div>
            </b-col>
          </b-row>
        </div>

        <b-table
            class="position-relative"
            :items="items"
            responsive
            :fields="headers"
            :busy.sync="dataLoading"
            primary-key="id"
            show-empty
            empty-text="Не найдено"
        >
            <template #cell(select)="row">
                <b-form-checkbox v-model="row.selected"></b-form-checkbox>
            </template>

            <template #cell()="data">
                <span v-html="data.value"></span>
            </template>

            <template #cell(actions)="row">
                <b-button size="sm" class="mr-1" variant="primary" @click="editCRUD(row.item.id)">
                    <feather-icon
                        icon="EditIcon"
                        size="21"
                    />
                </b-button>
                <b-button size="sm" variant="danger" @click="deleteCRUD(row.item.id)">
                    <feather-icon
                        icon="TrashIcon"
                        size="21"
                    />
                </b-button>
            </template>

            <template #table-busy>
                <div class="text-center text-primary my-2">
                    <b-spinner class="align-middle"></b-spinner>
                    <strong>Загрузка...</strong>
                </div>
            </template>
        </b-table>

          <b-row>
              <b-col sm="12">
                  <b-pagination
                      v-model="options.page"
                      :total-rows="totalItems"
                      :per-page="10"
                      align="center"
                  ></b-pagination>
              </b-col>
          </b-row>
      </b-card>

        <b-modal
            id="modal-create"
            v-model="dialogs.create"
            cancel-title="Отмена"
            ok-title="Создать"
            @ok="save"
            centered
            size="xl"
            title="Создание записи"
        >
            <b-form>
                <c-r-u-d-field :field="field" v-for="field in crud.create_fields" :value="create[field.name]" v-model="create[field.name]" v-bind:key="field.tkey"></c-r-u-d-field>
            </b-form>
        </b-modal>

        <b-modal
            id="modal-edit"
            v-model="dialogs.edit"
            cancel-title="Отмена"
            ok-title="Сохранить"
            @ok="saveEdit(edit.entry.id)"
            centered
            size="xl"
            title="Изменение записи"
        >
            <b-form v-model="edit_form">
                <c-r-u-d-field :field="field" v-for="field in edit.fields" :value="edit.entry[field.name]" v-model="edit.entry[field.name]" v-bind:key="field.tkey"></c-r-u-d-field>
            </b-form>
        </b-modal>

        <b-modal
            id="modal-delete"
            v-model="dialogs.delete"
            cancel-title="Отмена"
            ok-title="Удалить"
            ok-variant="danger"
            modal-class="modal-danger"
            @ok="deleteCRUD(delete_id, true)"
            centered
            title="Удаление записи"
        >
            <h4>Вы уверены что хотите удалить данную запись?</h4>
        </b-modal>
    </div>
</template>

<script>
    import api from "../api";
    import moment from "moment";

    import CRUDField from "../components/CRUDField";
    import {
      BCard, BRow, BCol, BFormInput, BButton, BTable, BMedia, BAvatar, BLink,
      BBadge, BDropdown, BDropdownItem, BPagination, BFormCheckbox, BSpinner, BForm
    } from 'bootstrap-vue'
    import _ from "lodash";

    export default {
        components:{
            CRUDField,
            BCard, BRow, BCol, BFormInput, BButton, BTable, BMedia, BAvatar, BLink,
            BBadge, BDropdown, BDropdownItem, BPagination, BFormCheckbox, BSpinner, BForm
        },
        data() {
            return {
                loaded: false,
                dataLoading: true,

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
                this.dataLoading = true;
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
                        this.dataLoading = false;
                    })
                    .catch(error => {
                        console.log(error);
                    });
            },
            loadCRUD() {
                this.loaded = false;
                api
                    .get("crud/" + this.$route.params.model)
                    .then(response => {
                        this.crud = response.data.crud;

                        this.crud.create_fields = response.data.create_fields;

                        this.headers.push({
                            key: 'select',
                            label: "",
                            sortable: false
                        });

                        for(const [key, item] of Object.entries(response.data.columns)){
                            this.headers.push({
                                key: item.key,
                                label: item.label,
                                align: "left",
                                sortable: item.orderable
                            });
                        }

                        for(const [key, item] of Object.entries(this.crud.create_fields)){
                            this.$set(this.create, key, null);
                        }

                        this.headers.push({
                            key: 'actions',
                            label: "Действия",
                            sortable: false
                        });

                        this.loaded = true;

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
