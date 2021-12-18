<template>
    <v-container fluid pt-0 grid-list-xl>
        <div class="mt-4 visitor-area-chart" v-if="this.hasPermission('dashboard_profit.monthly_chart.view')">
            <div class="d-custom-flex justify-space-between px-4 mb-4 label-wrap">
                <h4>Месячный доход за все время</h4>
                <!--<div class="d-custom-flex justify-space-between w-15">
                    <div class="w-50">
                        <h4 class="info--text mb-0">$ 35,455</h4>
                        <p class="fs-12 grey--text mb-0 fw-normal">International Visior</p>
                    </div>
                    <div class="w-100">
                        <h4 class="primary--text mb-0">$ 35,455</h4>
                        <p class="fs-12 grey--text mb-0 fw-normal">Доход за текущий год</p>
                    </div>
                </div>-->
            </div>
            <div class="px-4 pos-relative">
                <app-section-loader :status="!charts.profit_year.loaded"></app-section-loader>

                <visitors-stacked-area-chart v-if="charts.profit_year.loaded" :height="220"
                                             :labels="charts.profit_year.labels"
                                             :data="charts.profit_year.data">
                </visitors-stacked-area-chart>
            </div>
        </div>

        <section-tooltip title="Панель дохода"></section-tooltip>

        <v-layout row wrap>
            <app-card
                    heading="Выбор даты"
                    colClasses="xl8 lg7 md7 sm6 xs12"
                    customClasses="mb-0 sales-widget"
            >
                <date-range-picker time v-model="datarange"></date-range-picker>
            </app-card>
        </v-layout>

        <v-layout row wrap>
            <app-card v-if="this.hasPermission('dashboard_profit.daily_chart.view')"
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


                <!--<v-layout class="cart-wrap hidden-xs-only pl-4" v-if="charts.donate.loaded">
                    <v-flex d-custom-flex>
                        <span class="mr-2"><i class="zmdi zmdi-shopping-cart-plus primary--text"></i></span>
                        <p class="mb-0">
                            <span class="d-block fs-14 fw-bold">2382</span>
                            <span class="d-block fs-12 grey--text fw-normal">Sales</span>
                        </p>
                    </v-flex>
                    <v-flex d-custom-flex>
                        <span class="mr-2"><i class="zmdi zmdi-eye success--text"></i></span>
                        <p class="mb-0">
                            <span class="d-block fs-14 fw-bold">53,255</span>
                            <span class="d-block fs-12 grey--text fw-normal">Views</span>
                        </p>
                    </v-flex>
                    <v-flex d-custom-flex>
                        <span class="mr-2"><i class="zmdi zmdi-equalizer error--text"></i></span>
                        <p class="mb-0">
                            <span class="d-block fs-14 fw-bold">$1,25,000</span>
                            <span class="d-block fs-12 grey--text fw-normal">Earned</span>
                        </p>
                    </v-flex>
                </v-layout>-->
            </app-card>
            <app-card v-if="this.hasPermission('dashboard_profit.systems_chart.view')"
                    heading="Платежные системы"
                    colClasses="xl4 lg4 md4 sm6 xs12"
                    :fullScreen="true"
                    :reloadable="true"
                    :closeable="true"
                    customClasses="device-share-widget"
            >
                <app-section-loader :status="!charts.pay_systems.loaded"></app-section-loader>

                <doughnut-chart-v2 v-if="charts.pay_systems.loaded"
                                   :data="charts.pay_systems">
                </doughnut-chart-v2>
            </app-card>
        </v-layout>

        <v-layout row wrap>
            <app-card v-if="this.hasPermission('dashboard_profit.daily_groups.view')"
                    heading="Донат группы по дням"
                    colClasses="xl8 lg8 md6 sm12 xs12"
                    customClasses="device-share-widget"
            >
                <app-section-loader :status="!charts.groups.loaded"></app-section-loader>

                <bar-chart v-if="charts.groups.loaded"
                                   :height="240"
                                   :labels="charts.groups.labels"
                                   :datasets="charts.groups.datasets"
                >
                </bar-chart>
            </app-card>

            <app-card v-if="this.hasPermission('dashboard_profit.active_groups.view')"
                    heading="Активных донат групп"
                    colClasses="xl4 lg4 md6 sm12 xs12"
                    customClasses="device-share-widget"
            >
                <app-section-loader :status="!charts.groups_active.loaded"></app-section-loader>

                <doughnut-chart-v2 v-if="charts.groups_active.loaded"
                           :data="charts.groups_active"
                >
                </doughnut-chart-v2>
            </app-card>
        </v-layout>

        <v-layout row wrap>
            <app-card v-if="this.hasPermission('dashboard_profit.server_profit.view')"
                    heading="Доход по серверам"
                    colClasses="xl6 lg6 md6 sm12 xs12"
                    customClasses="device-share-widget"
            >
                <app-section-loader :status="!charts.server_profit.loaded"></app-section-loader>

                <sales-chart-v2 class="mb-4" :height="320" v-if="charts.server_profit.loaded"
                                :labels="charts.server_profit.labels"
                                :data1="charts.server_profit.data"
                >
                </sales-chart-v2>
            </app-card>

            <app-card v-if="this.hasPermission('dashboard_profit.branch_profit.view')"
                    heading="Доход по веткам"
                    colClasses="xl6 lg6 md6 sm12 xs12"
                    customClasses="device-share-widget"
            >
                <app-section-loader :status="!charts.branch_profit.loaded"></app-section-loader>

                <sales-chart-v2 class="mb-4" :height="320" v-if="charts.branch_profit.loaded"
                                :labels="charts.branch_profit.labels"
                                :data1="charts.branch_profit.data"
                >
                </sales-chart-v2>
            </app-card>
        </v-layout>
    </v-container>
</template>

<script>
    // chart config
    import LineChartShadow from "../components/Charts/LineChartShadow";
    import RecentComments from "../components/Widgets/RecentComments";
    import LineChart from "../views/charts/vue-chartjs/LineChart";
    import NewsLetterCampaign from "../components/Charts/NewsLetterCampaign";

    import { ChartConfig } from "Constants/chart-config";
    import api from "Api";
    import DoughnutChartV2 from "../components/Charts/DoughnutChartV2";
    import SalesChartV2 from "../components/Charts/SalesChartV2"
    import DateRangePicker from "../components/DateRangePicker"
    import VisitorsStackedAreaChart from "../components/Charts/VisitorsStackedAreaChart";

    import moment from "moment"

    import BarChart from "../components/BarChart"

    export default {
        mounted: function(){
            this.loadData();
        },
        components: {
            BarChart,
            VisitorsStackedAreaChart,
            DateRangePicker,
            DoughnutChartV2,
            SalesChartV2,
            LineChartShadow, RecentComments, LineChart, NewsLetterCampaign
        },
        methods:{
            loadData() {
                this.loadChart('donate');
                this.loadChart('pay_systems');
                this.loadChart('profit_year');
                this.loadChart('groups');
                this.loadChart('groups_active');
                this.loadChart('server_profit');
                this.loadChart('branch_profit');
            },
            openLink(link){
                window.open(link, "_blank");
            },
            loadChart(name){
                console.log('Load chart: ' + name);
                this.charts[name].loaded = false;

                api.post("dashboard/profit/chart", {
                    chart: name,
                    time: {
                        start: this.datarange.start.unix(),
                        end: this.datarange.end.unix(),
                    }
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
                    donate: {loaded:false, labels: [], data: []},
                    pay_systems: {loaded:false, labels: [], data: []},
                    profit_year: {loaded:false, labels: [], data: []},
                    groups: {loaded:false, labels: [], data: []},
                    groups_active: {loaded:false, labels: [], data: []},
                    server_profit: {loaded:false, labels: [], data: []},
                    branch_profit: {loaded:false, labels: [], data: []},
                },


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
