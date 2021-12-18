<template>
    <v-container fluid pt-0 grid-list-xl>
        <notifications></notifications>

        <section-tooltip title="Заявки в модераторы"></section-tooltip>

        <v-layout row wrap>
            <app-card
                    heading="Поиск по игроку"
                    colClasses="xs12 sm4"
                    customClasses="mb-0 sales-widget"
            >
                <user-selector v-model="search.user"></user-selector>
            </app-card>

            <app-card
                    heading="Поиск по дате"
                    colClasses="xs12 sm4"
                    customClasses="mb-0 sales-widget"
            >
                <date-range-picker id="charttime" time v-model="search.user"></date-range-picker>
            </app-card>

            <app-card
                    heading="Поиск по статусу"
                    colClasses="xs12 sm4"
                    customClasses="mb-0 sales-widget"
            >
                <v-select :items="status" v-model="search.status" item-text="label" item-value="value"></v-select>
            </app-card>
        </v-layout>

        <b-modal id="modalComment"
                 ref="modal"
                 v-model="comment.modal"
                 title="Установка комментария к заявке"
                 @ok="handleComment">
            <form @submit.stop.prevent="handleComment">
                <b-form-input type="text" placeholder="Введите комментарий" v-model="comment.text"></b-form-input>
            </form>
        </b-modal>

        <v-dialog v-model="modal.logs" transition="dialog-bottom-transition" overlay=false scrollable>
            <v-card>
                <v-toolbar color="primary" dark>
                    <v-btn icon @click.native="modal.logs = false" dark>
                        <v-icon>close</v-icon>
                    </v-btn>
                    <v-toolbar-title>Логи изменения записи</v-toolbar-title>
                    <v-spacer></v-spacer>
                    <v-toolbar-items>
                        <v-btn dark flat @click.native="modal.logs = false">Закрыть</v-btn>
                    </v-toolbar-items>
                </v-toolbar>
                <div style="flex: 1 1 auto;">
                    <table class="v-datatable v-table">
                        <tbody>
                            <tr>
                                <th>Администратор</th>
                                <th>Действие</th>
                                <th>Старые значения</th>
                                <th>Новые значения</th>
                            </tr>
                            <tr v-for="log in logs">
                                <td>{{ log.admin }}</td>
                                <td>{{ log.event }}</td>
                                <td>{{ JSON.stringify(log.old_values) }}</td>
                                <td>{{ JSON.stringify(log.new_values) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </v-card>
        </v-dialog>

        <v-layout row wrap>
            <app-card
                    heading="Заявки"
                    colClasses="xs12 sm12"
                    customClasses="mb-0 sales-widget"
            >
                <v-data-table
                    v-bind:headers="data.columns"
                    v-bind:items="data.rows"
                    hide-actions
                >
                    <template slot="items" slot-scope="props">
                        <td>
                            <a class="btn btn-xs btn-primary" @click.prevent="openAbout(props.item)">
                                <v-icon color="grey lighten-1" >search</v-icon>
                            </a>
                        </td>

                        <td v-for="key in Object.keys(props.item)" v-if="key !== 'id' && key !== 'about' && key !== 'contacts'">
                            <span v-html="props.item[key]"></span>
                        </td>

                        <td>
                            <v-btn icon class="mx-0" @click="loadLogs(props.item.id)" :disabled="!hasPermission('moder_entry.entry.edit')">
                                <v-icon color="grey lighten-1" >list</v-icon>
                            </v-btn>
                            <v-btn icon class="mx-0" @click="setComment(props.item.id)" :disabled="!hasPermission('moder_entry.entry.edit')">
                                <v-icon color="grey lighten-1" >edit</v-icon>
                            </v-btn>
                            <v-btn icon class="mx-0" @click="setStatus(props.item.id, 'ACCEPT')" :disabled="!hasPermission('moder_entry.entry.edit')">
                                <v-icon color="green lighten-1" >check</v-icon>
                            </v-btn>
                            <v-btn icon class="mx-0" @click="setStatus(props.item.id, 'DENY')" :disabled="!hasPermission('moder_entry.entry.edit')">
                                <v-icon color="grey lighten-1" >close</v-icon>
                            </v-btn>
                            <v-btn icon class="mx-0" @click="setStatus(props.item.id, 'DENY_FULL')" :disabled="!hasPermission('moder_entry.entry.edit')">
                                <v-icon color="red lighten-1" >close</v-icon>
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
</template>

<script>
    import UserSelector from "../components/UserSelector";
    import DateRangePicker from '../components/DateRangePicker';

    import api from 'Api';
    import moment from 'moment';

    import { mapGetters } from 'vuex';

    export default {
        name: "ModerEntry",
        components: {UserSelector, DateRangePicker},
        data: function () {
            return {
                status: [{label: 'Ожидание', value: 'WAIT'}, {label: 'Одобрена', value: 'ACCEPT'}, {label: 'Отклонена', value: 'DENY'}, {label: 'Отклонена без возможности повтора', value: 'DENY_FULL'}],
                search: {
                    filter: null,
                    user: null,
                    status: null,
                    time: {
                        start: moment().subtract(14, 'days').unix(),
                        end: moment().unix()
                    }
                },
                totalItems: 0,
                comment:{
                    id: null,
                    text: null,
                    modal: false
                },
                modal:{
                    logs: false
                },
                data:{
                    page: 1,
                    per_page: 10,
                    expanded: null,
                    columns: [],
                    rows: []
                },
                options: {
                    sortBy: "",
                    descending: false,
                    page: 1,
                    itemsPerPage: 10
                },
                logs: []
            }
        },
        computed:{
            ...mapGetters(["servers"]),
            startFrom(){
                return this.options.page > 1 ? (this.options.page - 1) * this.options.itemsPerPage : 0;
            },
            maxPages(){
                return Math.ceil(this.totalItems / this.options.itemsPerPage);
            }
        },
        methods:{
            loadLogs(id){
                this.modal.logs = false;

                api.post('moder/logs', {id: id})
                .then((response) => {
                    this.logs = response.data.logs;
                    console.log(this.logs);
                    this.modal.logs = true;
                });
            },
            openAbout(entry){
                alert(entry.about + '\n\nКонтакты: ' + entry.contacts);
            },
            formatUnix(timestamp){
                return moment.unix(timestamp).format('lll');
            },
            formatDate(date){
                return moment(date).format('lll');
            },
            findById(array, id){
                return array.find(obj => obj.id === id);
            },
            reload: function(){
                api.post('moder/search', {time: this.search.time, user: this.search.user ? this.search.user.id : null, status: this.search.status ? this.search.status.value : 'WAIT'})
                    .then((response) => {
                        this.data.columns = response.data.columns;
                        this.data.rows = response.data.rows;
                    });
            },
            setStatus: function (id, status) {
                this.sendRequest('moder/status', {id: id, status: status});
            },
            setComment: function (id) {
                this.comment.id = id;
                this.comment.modal = true;
            },
            handleComment () {
                this.$refs.modal.hide();

                this.sendRequest('/admin/moder/comment', {id: this.comment.id, text: this.comment.text});

                this.comment.id = null;
                this.comment.text = null;
            },
            sendRequest(url, data, callback){
                api.post(url, data)
                    .then(response => {
                        if (callback !== null){
                            callback(response);
                        }
                    })
                    .catch(err => {
                        console.log(err);
                    });
            },
        },
        mounted(){
            this.reload();
        },
        watch: {
            'search.user': function (val) {
                this.reload();
            },
            'search.status': function (val) {
                this.reload();
            },
            'search.time': function (val) {
                this.reload();
            }
        }
    }
</script>