<template>
    <v-container fluid pt-0 grid-list-xl>
        <section-tooltip title="Логи пользователей и админ-панели"></section-tooltip>

        <v-layout row wrap>
            <app-card
                    heading="Выбор даты"
                    colClasses="xl4 lg4 md4 sm4 xs12"
                    customClasses="mb-0 sales-widget"
            >
                <date-range-picker time v-model="datarange"></date-range-picker>
            </app-card>
            <app-card
                    heading="Поиск по игроку"
                    colClasses="xl4 lg4 md4 sm4 xs12"
                    customClasses="mb-0 sales-widget"
            >
                <user-selector v-model="selected_user"></user-selector>
            </app-card>
            <app-card
                    heading="Поиск по типу действия"
                    colClasses="xl4 lg4 md4 sm4 xs12"
                    customClasses="mb-0 sales-widget"
            >
                <v-select v-model="selected_type" :items="type_options" item-text="title" item-value="value"></v-select>
            </app-card>
        </v-layout>

        <app-section-loader :status="this.loading"></app-section-loader>

        <v-layout row wrap v-if="!this.loading">
             <app-card
                     heading="Действия"
                     colClasses="xs12 sm12"
             >
                 <v-timeline dense="">
                     <v-timeline-item v-for="action in actions" :key="Math.random()" :icon="action.icon">
                         <v-card>
                             <v-card-title class="title">[{{ action.login }}] {{ action.header }}</v-card-title>
                             <v-card-text v-html="action.body">

                             </v-card-text>
                             <v-card-actions>
                                 Время: {{ action.time }}
                             </v-card-actions>
                         </v-card>
                     </v-timeline-item>
                 </v-timeline>

             </app-card>
         </v-layout>
    </v-container>
</template>

<script>
    import api from "Api";
    import moment from "moment"
    import DateRangePicker from "../components/DateRangePicker"
    import UserSelector from "../components/UserSelector";

    export default {
        name: "Logs",
        components: {UserSelector, DateRangePicker},
        data() {
            return {
                loading: false,

                type_options: [
                    { title: 'Пополнение счета', value: 'unitpay_add'},
                    { title: 'Покупка группы', value: 'buygroup'},
                    { title: 'Покупка предмета', value: 'buyitem'},
                    { title: 'Покупка разбана', value: 'buy_unban'},
                    { title: 'Смена пароля', value: 'changepass'},
                    { title: 'Включение 2FA', value: 'enableotp'},
                    { title: 'Обмен валюты', value: 'exchange'},
                    { title: 'Передача денег игроку', value: 'sendplayer'},
                    { title: 'Покупка коинов на сервер', value: 'sendserver'},
                    { title: 'Перевод денег на сайт', value: 'sendsite'},
                    { title: 'Установка префикса', value: 'setprefix'},
                ],

                datarange: {
                    start: moment().subtract(14, 'days').startOf('day'),
                    end: moment()
                },
                selected_user: null,
                selected_type: null,

                actions: []
            }
        },
        methods:{
            loadData(){
                this.loading = true;

                api.post("logs/user", {
                    user: this.selected_user ? this.selected_user.id : null,
                    type: this.selected_type,
                    time: {
                        start: this.datarange.start.unix(),
                        end: this.datarange.end.unix(),
                    }
                }).then(response => {
                    this.actions = response.data.actions;
                    this.loading = false;
                });
            }
        },
        watch:{
            datarange: function () {
                this.loadData();
            },
            selected_user: function () {
                this.loadData();
            },
            selected_type: function () {
                this.loadData();
            }
        }
    }
</script>

<style scoped>

</style>