<template>
    <v-container fluid pt-0 grid-list-xl>
        <notifications></notifications>

        <section-tooltip title="Форумный менеджер"></section-tooltip>

        <v-layout row wrap>
            <app-card
                    heading="Выбор игрока"
                    colClasses="xs12 sm4"
                    customClasses="mb-0 sales-widget"
            >
                <user-selector v-model="user"></user-selector>
            </app-card>

            <app-card
                    heading="Выбор сервера"
                    colClasses="xs12 sm4"
                    customClasses="mb-0 sales-widget"
            >
                <v-select item-value="server" item-text="server" class="form-control" label="name" :items="servers" v-model="server"></v-select>
            </app-card>

            <app-card
                    heading="Выбор даты"
                    colClasses="xs12 sm4"
                    customClasses="mb-0 sales-widget"
            >
                <date-range-picker id="charttime" time v-model="time"></date-range-picker>
            </app-card>
        </v-layout>

        <v-layout row wrap>
            <app-card
                    heading="Общий онлайн за дату"
                    colClasses="xs12 sm4"
                    customClasses="mb-0 sales-widget"
            >
                <doughnut-chart-v2 :data="charts.server_time.data" v-if="charts.server_time.loaded"></doughnut-chart-v2>
            </app-card>

            <app-card v-if="server"
                    :heading="'Онлайн игроков на ' + server"
                    colClasses="xs12 sm4"
                    customClasses="mb-0 sales-widget"
            >
                <doughnut-chart-v2 :data="charts.server_user_time.data" v-if="charts.server_user_time.loaded"></doughnut-chart-v2>
            </app-card>

            <app-card v-if="user"
                    :heading="'Онлайн ' + user.login + ' на серверах'"
                    colClasses="xs12 sm4"
                    customClasses="mb-0 sales-widget"
            >
                <doughnut-chart-v2 :data="charts.user_server_time.data" v-if="charts.user_server_time.loaded"></doughnut-chart-v2>
            </app-card>

        </v-layout>
    </v-container>
</template>

<script>
    import UserSelector from '../components/UserSelector';
    import DateRangePicker from '../components/DateRangePicker';

    import api from 'Api';
    import * as moment from 'moment';
    import DoughnutChartV2 from "../components/Charts/DoughnutChartV2";

    export default {
        name: "Online",
        components: {DoughnutChartV2, UserSelector, DateRangePicker},
        data: function() { return {
            user: null,
            time: {
                start: moment().subtract(7, 'days'),
                end: moment()
            },
            server: null,
            servers: [],
            charts:{
                server_time: {
                    loaded: false,
                    data: {},
                },
                server_user_time: {
                    loaded: false,
                    data: {},
                },
                user_server_time: {
                    loaded: false,
                    data: {},
                }
            },
            profit: {
                time: null,
                server: null
            }
        }},

        methods:{
            reloadAllCharts: function() {
                this.reloadChart('server_time');
                this.reloadChart('server_user_time');
                this.reloadChart('user_server_time');
            },
            reloadChart: function(name) {
                this.charts[name].loaded = false;

                var data = {chart: name, time: {
                    start: moment(this.time.start).unix(),
                    end: moment(this.time.end).unix(),
                }};
                if (this.user) data.user = this.user.id;
                if (this.server) data.server = this.server;

                api.post('online/chart', data)
                    .then(response => {
                        this.charts[name].data = response.data.data;
                        this.charts[name].loaded = true;
                    })
                    .catch(err => {
                        console.log(err);
                    });
            },
            toHHMMSS: (secs) => {
                var sec_num = parseInt(secs, 10);
                var hours   = Math.floor(sec_num / 3600) % 24;
                var minutes = Math.floor(sec_num / 60) % 60;
                var seconds = sec_num % 60;
                return [hours,minutes,seconds]
                    .map(v => v < 10 ? "0" + v : v)
                    .filter((v,i) => v !== "00" || i > 0)
                    .join(":");
            }
        },
        async mounted(){
            api.post('online/servers')
                .then(response => {
                    this.servers = response.data.servers;
                })
                .catch(err => {
                    console.log(err);
                });

            this.$nextTick(() => {
                this.reloadAllCharts();
            });
        },
        watch:{
            'time': function (val) {
                this.reloadAllCharts();
            },
            'user': function (val) {
                this.reloadAllCharts();
            },
            'server': function (val) {
                this.reloadAllCharts();
            }
        }
    }
</script>

<style scoped>

</style>