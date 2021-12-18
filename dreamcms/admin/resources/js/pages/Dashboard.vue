<template>
    <v-container fluid pt-0 grid-list-xl>
        <section-tooltip title="Статистика за сегодня"></section-tooltip>

        <v-layout row wrap border-rad-sm overflow-hidden v-if="this.hasPermission('dashboard.stat.view')">
            <v-flex xl4 lg4 md4 sm4 xs12 b-50>
                <app-section-loader :status="!cards.loaded"></app-section-loader>

                <stats-card-v4 v-if="cards.loaded"
                        title="Регистраций"
                        :value="cards.registers.count"
                        :data="cards.registers.chart.data"
                        :labels="cards.registers.chart.labels"
                        :color="ChartConfig.color.info"
                >
                </stats-card-v4>
            </v-flex>
            <v-flex xl4 lg4 md4 sm4 xs12 b-50>
                <app-section-loader :status="!cards.loaded"></app-section-loader>

                <stats-card-v4 v-if="cards.loaded"
                        title="Покупок групп"
                        :value="cards.group_buys.count"
                        :data="cards.group_buys.chart.data"
                        :labels="cards.group_buys.chart.labels"
                        :color="ChartConfig.color.warning"
                >
                </stats-card-v4>
            </v-flex>
            <v-flex xl4 lg4 md4 sm4 xs12 b-50>
                <app-section-loader :status="!cards.loaded"></app-section-loader>

                <stats-card-v4 v-if="cards.loaded"
                        title="Покупок предметов"
                        :value="cards.shop_buys.count"
                        :data="cards.shop_buys.chart.data"
                        :labels="cards.shop_buys.chart.labels"
                        :color="ChartConfig.color.success"
                >
                </stats-card-v4>
            </v-flex>
        </v-layout>

        <v-layout row wrap>
            <app-card v-if="this.hasPermission('dashboard.password_chart.view')"
                    heading="Смены пароля"
                    colClasses="xl6 lg6 md6 sm12 xs12"
            >
                <app-section-loader :status="!charts.loaded"></app-section-loader>
                <news-letter-campaign v-if="charts.loaded"
                            :width="370"
                            :height="360"
                            :labels="charts.changepass.labels"
                            label1="Успешных смены пароля"
                            label2="Запросов смены пароля"
                            :data1="charts.changepass.data1"
                            :data2="charts.changepass.data2"
                >
                </news-letter-campaign>
            </app-card>
            <app-card
                    heading="Последние сообщения с форума"
                    :closeable="true"
                    :fullScreen="true"
                    :reloadable="true"
                    :fullBlock="true"
                    :footer="true"
                    colClasses="xl6 lg6 md6 sm12 xs12"
            >
                <recent-comments></recent-comments>
                <div slot="footer">
                    <v-btn small color="primary" @click="openLink('/forum')">На форум</v-btn>
                </div>
            </app-card>
        </v-layout>

        <v-layout row wrap v-if="this.hasPermission('dashboard.bans_chart.view')">
            <app-card
                    heading="Причины банов"
                    colClasses="xl6 lg6 md6 sm12 xs12"
            >
                <app-section-loader :status="!charts.loaded"></app-section-loader>
                <doughnut-chart-v2 v-if="charts.loaded"
                        :height="240"
                        :data="charts.bans_reasons.data">
                </doughnut-chart-v2>
            </app-card>
            <app-card
                    heading="Блокировки игроков"
                    colClasses="xl6 lg6 md6 sm12 xs12"
            >
                <app-section-loader :status="!charts.loaded"></app-section-loader>
                <news-letter-campaign v-if="charts.loaded"
                                      :width="370"
                                      :height="360"
                                      :labels="charts.bans.labels"
                                      label1="Окончание блокировок"
                                      label2="Начало блокировок"
                                      :data1="charts.bans.data2"
                                      :data2="charts.bans.data1"
                >
                </news-letter-campaign>
            </app-card>
        </v-layout>

        <v-layout row wrap v-if="this.hasPermission('dashboard.2fa_chart.view')">
            <app-card
                    heading="2FA "
                    colClasses="xl6 lg6 md6 sm12 xs12"
            >
                <app-section-loader :status="!charts.loaded"></app-section-loader>
                <news-letter-campaign v-if="charts.loaded"
                                      :width="370"
                                      :height="360"
                                      :labels="charts.otp.labels"
                                      label1="Подключений"
                                      label2="Отключений"
                                      :data1="charts.otp.data1"
                                      :data2="charts.otp.data2"
                >
                </news-letter-campaign>
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

    export default {
        created: function(){
            this.loadData();
        },
        components: {
            DoughnutChartV2,
            LineChartShadow, RecentComments, LineChart, NewsLetterCampaign
        },
        methods:{
            loadData() {
                api.post("dashboard/load")
                    .then(response => {
                        this.cards = response.data.cards;
                        this.cards.loaded = true;
                    })
                    .catch(error => {
                        console.log("Error " + error);
                    });

                api.post("dashboard/charts")
                    .then(response => {
                        this.charts = response.data.charts;
                        this.charts.loaded = true;
                    })
                    .catch(error => {
                        console.log("Error " + error);
                    });
            },
            openLink(link){
                window.open(link, "_blank");
            }
        },
        data() {
            return {
                cards:{
                    loaded: false,
                    registers: { count: 0, chart: [] },
                    group_buys: { count: 0, chart: [] },
                    shop_buys: { count: 0, chart: [] },
                },

                charts:{
                    loaded: false,
                    changepass: {labels: [], data: []},
                    bans: {labels: [], data: []},
                    bans_reasons: {labels: [], data: []},
                    otp: {labels: [], data: []},
                },

                ChartConfig
            };
        }
    };
</script>
