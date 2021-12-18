<template>
    <v-container fluid pt-0 grid-list-xl>
        <section-tooltip title="Реферальная система"></section-tooltip>

        <v-layout row wrap>
            <app-card
                    heading="Выбор даты"
                    colClasses="xl6 lg6 md6 sm12 xs12"
                    customClasses="mb-0 sales-widget"
            >
                <date-range-picker time v-model="datarange"></date-range-picker>
            </app-card>

            <app-card v-if="this.hasPermission('refer.other.view')"
                      heading="Выбор игрока"
                      colClasses="xl6 lg6 md6 sm12 xs12"
                      customClasses="mb-0 sales-widget"
            >
                <user-selector @selected="onSelectUser"></user-selector>
            </app-card>
        </v-layout>

        <v-layout row wrap>
            <app-card
                      heading="Дневной доход"
                      colClasses="xl8 lg8 md8 sm6 xs12"
                      customClasses="mb-0 sales-widget"
                      :fullScreen="true"
                      :reloadable="true"
            >
                <app-section-loader :status="!charts.donate.loaded"></app-section-loader>

                <div class="app-flex justify-start align-start" v-if="charts.donate.loaded">
                    <v-icon :class="percent_grow >= 0 ? 'success--text' : 'warning--text' + ' mr-3 font-2x'">{{ percent_grow >= 0 ? 'arrow_upward' : 'arrow_downward'}}</v-icon>
                    <div>
                        <h2 class="mb-0" v-if="charts.donate.today">{{ charts.donate.today }}₽ за сегодня</h2>
                        <h2 v-else>0₽ за сегодня</h2>

                        <h2 class="mb-0" v-if="charts.donate.this_period">{{ charts.donate.this_period }}₽ за выбранные даты</h2>
                        <h2 class="mb-0" v-else>0₽ за выбранные даты</h2>

                        <span class="fs-14 gray--text">{{ percent_grow }}% {{ percent_grow >= 0 ? 'прирост' : 'убыль'}} с прошлого периода ({{ charts.donate.last_period }}₽)</span>
                    </div>
                </div>

                <sales-chart-v2 class="mb-4" :height="320" v-if="charts.donate.loaded"
                                :labels="charts.donate.labels"
                                :data1="charts.donate.data1"
                                :data2="charts.donate.data2"
                >

                </sales-chart-v2>
            </app-card>
        </v-layout>
    </v-container>
</template>

<script>
    import { ChartConfig } from "Constants/chart-config";
    import api from "Api";
    import SalesChartV2 from "../components/Charts/SalesChartV2"
    import DateRangePicker from "../components/DateRangePicker"

    import moment from "moment"


    import UserSelector from "../components/UserSelector";

    export default {
        mounted: function(){
            this.loadData();
        },
        components: {
            DateRangePicker,
            SalesChartV2,
            UserSelector
        },
        methods:{
            loadData() {
                this.loadChart('donate');
            },
            onSelectUser(user){
                this.user = user.id;

                this.loadData();
            },
            loadChart(name){
                console.log('Load chart: ' + name);
                this.charts[name].loaded = false;

                api.post("refer/chart", {
                    chart: name,
                    time: {
                        start: this.datarange.start.unix(),
                        end: this.datarange.end.unix(),
                    },
                    user: this.user
                })
                    .then(response => {
                        this.charts[name] = response.data.chart;
                        this.charts[name].loaded = true;
                    })
                    .catch(error => {
                        console.log("Error " + error);
                    });
            }
        },
        computed:{
            percent_grow(){
                return Math.round((this.charts.donate.this_period - this.charts.donate.last_period) / this.charts.donate.last_period * 100);
            }
        },
        data() {
            return {
                datarange: {
                    start: moment().subtract(14, 'days').startOf('day'),
                    end: moment()
                },
                charts:{
                    donate: {loaded:false, labels: [], data: []}
                },

                user: false,

                ChartConfig
            };
        },
        watch:{
            datarange: function (val) {
                this.loadData();
            }
        }
    };
</script>
