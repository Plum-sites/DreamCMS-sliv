<template>
    <section id="dashboard-analytics">
<!--        <b-row class="match-height">-->
<!--            <b-col-->
<!--                sm="12"-->
<!--            >-->
<!--                <b-card-->
<!--                    no-body-->
<!--                    class="card-statistics"-->
<!--                >-->
<!--                    <b-card-header>-->
<!--                        <b-card-title>Статистика</b-card-title>-->
<!--                    </b-card-header>-->
<!--                    <b-card-body class="statistics-body">-->
<!--                        <b-row>-->
<!--                            <b-col-->
<!--                                v-for="item in statisticsItems"-->
<!--                                :key="item.icon"-->
<!--                                md="3"-->
<!--                                sm="6"-->
<!--                                class="mb-2 mb-md-0"-->
<!--                                :class="item.customClass"-->
<!--                            >-->
<!--                                <b-media no-body>-->
<!--                                    <b-media-aside-->

<!--                                        class="mr-2"-->
<!--                                    >-->
<!--                                        <b-avatar-->
<!--                                            size="48"-->
<!--                                            :variant="item.color"-->
<!--                                        >-->
<!--                                            <feather-icon-->
<!--                                                size="24"-->
<!--                                                :icon="item.icon"-->
<!--                                            />-->
<!--                                        </b-avatar>-->
<!--                                    </b-media-aside>-->
<!--                                    <b-media-body class="my-auto">-->
<!--                                        <h4 class="font-weight-bolder mb-0">-->
<!--                                            {{ item.title }}-->
<!--                                        </h4>-->
<!--                                        <b-card-text class="font-small-3 mb-0">-->
<!--                                            {{ item.subtitle }}-->
<!--                                        </b-card-text>-->
<!--                                    </b-media-body>-->
<!--                                </b-media>-->
<!--                            </b-col>-->
<!--                        </b-row>-->
<!--                    </b-card-body>-->
<!--                </b-card>-->
<!--            </b-col>-->
<!--        </b-row>-->
        <b-row class="match-height">
            <b-col
                lg="3"
                sm="6"
            >
                <statistic-card-with-area-chart
                    v-if="charts.registers"
                    icon="UsersIcon"
                    :statistic="charts.registers.today"
                    statistic-title="Регистрации"
                    :chart-data="charts.registers.series"
                />
            </b-col>
            <b-col
                lg="3"
                sm="6"
            >
                <statistic-card-with-area-chart
                    v-if="charts.players"
                    icon="ShoppingBagIcon"
                    color="warning"
                    :statistic="charts.players.today"
                    statistic-title="Входов в игру"
                    :chart-data="charts.players.series"
                />
            </b-col>
            <b-col
                lg="3"
                sm="6"
            >
                <statistic-card-with-area-chart
                    v-if="charts.posts"
                    icon="SendIcon"
                    color="success"
                    :statistic="charts.posts.today"
                    statistic-title="Сообщений на форуме"
                    :chart-data="charts.posts.series"
                />
            </b-col>
            <b-col
                lg="3"
                sm="6"
            >
                <statistic-card-with-area-chart
                    v-if="charts.moders"
                    icon="UserPlusIcon"
                    color="danger"
                    :statistic="charts.moders.today"
                    statistic-title="Модер заявок"
                    :chart-data="charts.moders.series"
                />
            </b-col>
        </b-row>

        <b-row class="match-height">
            <b-col sm="12" md="6">
                <b-card no-body v-if="charts.password_chart">
                    <b-card-header>
                        <b-card-title class="mb-50">
                            Смены пароля
                        </b-card-title>

                        <div class="d-flex align-items-center">
                            <feather-icon
                                icon="CalendarIcon"
                                size="16"
                            />
                            <flat-pickr
                                v-model="rangePicker"
                                :config="rangePickerConfig"
                                class="form-control flat-picker bg-transparent border-0 shadow-none"
                                placeholder="DD-MM-YYYY"
                            />
                        </div>
                    </b-card-header>

                    <b-card-body>
                        <vue-apex-charts
                            type="bar"
                            height="400"
                            :options="charts.password_chart.chartOptions"
                            :series="charts.password_chart.series"
                        />
                    </b-card-body>
                </b-card>
            </b-col>
            <b-col sm="12" md="6">
                <b-card no-body v-if="charts.twofa_chart">
                    <b-card-header>
                        <b-card-title class="mb-50">
                            Двухэтапная авторизация
                        </b-card-title>

                        <div class="d-flex align-items-center">
                            <feather-icon
                                icon="CalendarIcon"
                                size="16"
                            />
                            <flat-pickr
                                v-model="rangePicker"
                                :config="rangePickerConfig"
                                class="form-control flat-picker bg-transparent border-0 shadow-none"
                                placeholder="DD-MM-YYYY"
                            />
                        </div>
                    </b-card-header>

                    <b-card-body>
                        <vue-apex-charts
                            type="area"
                            height="400"
                            :options="charts.twofa_chart.chartOptions"
                            :series="charts.twofa_chart.series"
                        />
                    </b-card-body>
                </b-card>
            </b-col>
        </b-row>

        <b-row>
            <b-col sm="12" md="8">
                <b-card no-body v-if="charts.bans_chart">
                    <b-card-header>
                        <b-card-title class="mb-50">
                            Баны
                        </b-card-title>

                        <div class="d-flex align-items-center">
                            <feather-icon
                                icon="CalendarIcon"
                                size="16"
                            />
                            <flat-pickr
                                v-model="rangePicker"
                                :config="rangePickerConfig"
                                class="form-control flat-picker bg-transparent border-0 shadow-none"
                                placeholder="DD-MM-YYYY"
                            />
                        </div>
                    </b-card-header>

                    <b-card-body>
                        <vue-apex-charts
                            type="area"
                            height="400"
                            :options="charts.bans_chart.chartOptions"
                            :series="charts.bans_chart.series"
                        />
                    </b-card-body>
                </b-card>
            </b-col>
            <b-col sm="12" md="4">
                <b-card v-if="charts.bans_reasons">
                    <b-card-title class="mb-1">
                        Причины бана
                    </b-card-title>

                    <vue-apex-charts
                        type="donut"
                        height="400"
                        :options="charts.bans_reasons.chartOptions"
                        :series="charts.bans_reasons.series"
                    />
                </b-card>
            </b-col>
        </b-row>
    </section>
</template>

<script>
    import { BRow, BCol, BCard, BCardHeader, BCardBody, BCardSubTitle, BCardTitle,
        BCardText, BMedia, BMediaAside, BAvatar, BMediaBody,
    } from 'bootstrap-vue'

    import StatisticCardWithAreaChart from '@core/components/statistics-cards/StatisticCardWithAreaChart.vue'

    import VuePerfectScrollbar from 'vue-perfect-scrollbar'
    import VueApexCharts from 'vue-apexcharts'
    import { $themeColors } from '@themeConfig'
    import flatPickr from 'vue-flatpickr-component'
    import {Russian} from 'flatpickr/dist/l10n/ru.js';
    import moment from "moment";

    import api from "../api";

    const chartColors = {
        column: {
            series1: '#826af9',
            series2: '#d2b0ff',
            bg: '#f8d3ff',
        },
        success: {
            shade_100: '#7eefc7',
            shade_200: '#06774f',
        },
        donut: {
            series1: '#ffe700',
            series2: '#00d4bd',
            series3: '#826bf8',
            series4: '#2b9bf4',
            series5: '#FFA1A1',
        },
        area: {
            series3: '#a4f8cd',
            series2: '#60f2ca',
            series1: '#2bdac7',
        },
    };

    export default {
        components: {
            flatPickr,
            BCol, BRow, BCard, BCardHeader, BCardBody, BCardSubTitle, BCardTitle,
            BCardText, BMedia, BMediaAside, BAvatar, BMediaBody,
            VueApexCharts,
            VuePerfectScrollbar,
            StatisticCardWithAreaChart
        },
        created: function(){
            this.loadCharts();
        },
        methods:{
            loadCharts() {
                var charts = ['registers', 'players', 'posts', 'moders', 'password_chart', 'bans_chart', 'bans_reasons', 'twofa_chart'];

                charts.forEach(chart => {
                    this.$set(this.charts, chart, null);

                    api.post("dashboard/chart", {
                        chart: chart,
                        range: this.rangePicker && this.rangePicker.includes('—') ? this.rangePicker : null
                    }).then(response => {
                        if (response.data.success){
                            if (this.options[chart]){
                                response.data.chart.chartOptions = {...response.data.chart.chartOptions, ...this.options[chart]};
                            }

                            this.$set(this.charts, chart, response.data.chart);
                        }
                    })
                });
            },
            openLink(link){
                window.open(link, "_blank");
            }
        },
        data() {
            return {
                statisticsItems: [
                    {
                        icon: 'TrendingUpIcon',
                        color: 'light-primary',
                        title: '230k',
                        subtitle: 'Sales',
                        customClass: 'mb-2 mb-xl-0',
                    },
                    {
                        icon: 'UserIcon',
                        color: 'light-info',
                        title: '8.549k',
                        subtitle: 'Customers',
                        customClass: 'mb-2 mb-xl-0',
                    },
                    {
                        icon: 'BoxIcon',
                        color: 'light-danger',
                        title: '1.423k',
                        subtitle: 'Products',
                        customClass: 'mb-2 mb-sm-0',
                    },
                    {
                        icon: 'DollarSignIcon',
                        color: 'light-success',
                        title: '$9745',
                        subtitle: 'Revenue',
                        customClass: '',
                    },
                ],
                charts: {},
                rangePicker: null,
                rangePickerConfig: {
                    mode: 'range',
                    //dateFormat: 'd-m-Y',
                    locale: Russian,
                    maxDate: "today",

                    defaultDate: [moment().subtract(2, "week").toDate(), moment().toDate()],
                },

                options:{
                    password_chart: {
                        chart: {
                            stacked: true,
                            toolbar: {
                                show: false,
                            },
                        },
                        colors: [
                            chartColors.column.series1,
                            chartColors.column.series2
                        ],
                        plotOptions: {
                            bar: {
                                columnWidth: '50%',
                                colors: {
                                    backgroundBarRadius: 10,
                                },
                            },
                        },
                        dataLabels: {
                            enabled: false,
                        },
                        legend: {
                            show: true,
                            position: 'top',
                            fontSize: '14px',
                            fontFamily: 'Montserrat',
                            horizontalAlign: 'left',
                        },
                        stroke: {
                            show: true,
                            colors: ['transparent'],
                        },
                        grid: {
                            xaxis: {
                                lines: {
                                    show: true,
                                },
                            },
                        },
                        fill: {
                            opacity: 1,
                        },
                    },
                    bans_chart: {
                        chart: {
                            toolbar: {
                                show: false,
                            },
                        },
                        dataLabels: {
                            enabled: false,
                        },
                        stroke: {
                            show: false,
                            curve: 'straight',
                        },
                        legend: {
                            show: true,
                            position: 'top',
                            horizontalAlign: 'left',
                            fontSize: '14px',
                            fontFamily: 'Montserrat',
                        },
                        grid: {
                            xaxis: {
                                lines: {
                                    show: true,
                                },
                            },
                        },
                        fill: {
                            opacity: 1,
                            type: 'solid',
                        },
                        tooltip: {
                            shared: false,
                        },
                        colors: [chartColors.area.series3, chartColors.area.series2, chartColors.area.series1],
                    },
                    twofa_chart: {
                        chart: {
                            toolbar: {
                                show: false,
                            },
                        },
                        dataLabels: {
                            enabled: false,
                        },
                        stroke: {
                            show: false,
                            curve: 'straight',
                        },
                        legend: {
                            show: true,
                            position: 'top',
                            horizontalAlign: 'left',
                            fontSize: '14px',
                            fontFamily: 'Montserrat',
                        },
                        grid: {
                            xaxis: {
                                lines: {
                                    show: true,
                                },
                            },
                        },
                        fill: {
                            opacity: 1,
                            type: 'solid',
                        },
                        tooltip: {
                            shared: false,
                        },
                        colors: ['#60f2ca', '#826bf8'],
                    },
                    bans_reasons:{
                        legend: {
                            show: true,
                            position: 'bottom',
                            fontSize: '14px',
                            fontFamily: 'Montserrat',
                        },
                        colors: [
                            chartColors.donut.series1,
                            chartColors.donut.series2,
                            chartColors.donut.series3,
                            chartColors.donut.series4,
                            chartColors.donut.series5,
                        ],
                        dataLabels: {
                            enabled: true
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    labels: {
                                        show: true,
                                        name: {
                                            fontSize: '2rem',
                                            fontFamily: 'Montserrat',
                                        },
                                        value: {
                                            fontSize: '1rem',
                                            fontFamily: 'Montserrat'
                                        },
                                        total: {
                                            show: false
                                        },
                                    },
                                },
                            },
                        },
                        responsive: [
                            {
                                breakpoint: 992,
                                options: {
                                    chart: {
                                        height: 400,
                                    },
                                    legend: {
                                        position: 'bottom',
                                    },
                                },
                            },
                            {
                                breakpoint: 576,
                                options: {
                                    chart: {
                                        height: 400,
                                    },
                                    plotOptions: {
                                        pie: {
                                            donut: {
                                                labels: {
                                                    show: true,
                                                    name: {
                                                        fontSize: '1.5rem',
                                                    },
                                                    value: {
                                                        fontSize: '1rem',
                                                    },
                                                    total: {
                                                        fontSize: '1.5rem',
                                                    },
                                                },
                                            },
                                        },
                                    },
                                    legend: {
                                        show: true,
                                    },
                                },
                            },
                        ],

                    }
                }
            };
        },
        watch:{
            rangePicker: function (newVal) {
                this.loadCharts();
            }
        }
    };
</script>

<style lang="scss">
@import '@core/scss/vue/libs/vue-flatpicker.scss';
@import '@core/scss/vue/libs/chart-apex.scss';
</style>