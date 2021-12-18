<template>
  <section id="online">
    <b-row class="match-height">
      <b-col
          sm="12"
          lg="4"
      >
        <b-card>
          <b-card-title>
            Выбор игрока
          </b-card-title>
          <user-selector v-model="user"></user-selector>
        </b-card>
      </b-col>

      <b-col
          sm="12"
          lg="4"
      >
        <b-card>
          <b-card-title>
            Выбор сервера
          </b-card-title>
          <v-select v-model="server" :options="servers" label="server"></v-select>
        </b-card>
      </b-col>

      <b-col
          sm="12"
          lg="4"
      >
        <b-card>
          <b-card-title>
            Выбор даты
          </b-card-title>
          <flat-pickr
              v-model="time"
              :config="rangePickerConfig"
              class="form-control"
          />
        </b-card>
      </b-col>
    </b-row>

    <b-row class="match-height">
      <b-col sm="12" md="4">
        <b-card v-if="charts.server_time">
          <b-card-title class="mb-1">
            Общее время на серверах
          </b-card-title>

          <vue-apex-charts
              type="donut"
              height="400"
              :options="charts.server_time.chartOptions"
              :series="charts.server_time.series"
          />
        </b-card>
      </b-col>

      <b-col sm="12" md="4">
        <b-card v-if="charts.server_user_time">
          <b-card-title class="mb-1">
            Игроки на сервере
          </b-card-title>

          <vue-apex-charts
              type="donut"
              height="400"
              :options="charts.server_user_time.chartOptions"
              :series="charts.server_user_time.series"
          />
        </b-card>
      </b-col>

      <b-col sm="12" md="4">
        <b-card v-if="charts.user_server_time">
          <b-card-title class="mb-1">
            Онлайн игрока по серверам
          </b-card-title>

          <vue-apex-charts
              type="donut"
              height="400"
              :options="charts.user_server_time.chartOptions"
              :series="charts.user_server_time.series"
          />
        </b-card>
      </b-col>
    </b-row>
  </section>
</template>

<script>
    import UserSelector from "../components/UserSelector";
    import DateRangePicker from '../components/DateRangePicker';
    import vSelect from 'vue-select';

    import api from '../api';
    import {Russian} from 'flatpickr/dist/l10n/ru.js';
    import moment from "moment";
    import flatPickr from 'vue-flatpickr-component'
    import VueApexCharts from 'vue-apexcharts'

    import {
      BCard, BButton, BRow, BCol, BSpinner, BCardTitle,
    } from 'bootstrap-vue'

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
        name: "Online",
        components: {
          flatPickr,
          UserSelector,
          DateRangePicker,
          BCard, BButton, BRow, BCol, BSpinner, BCardTitle, vSelect,
          VueApexCharts
        },
        data: function() { return {
            user: null,

            rangePickerConfig: {
              mode: 'range',
              locale: Russian,
              defaultDate: [moment().subtract(31, "day").toDate(), moment().toDate()],
            },

            time: null,
            server: null,
            servers: [],
            charts:{},

            options:{
              server_time:{
                legend: {
                  show: false,
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
              },
              server_user_time:{
                legend: {
                  show: false,
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

              },
              user_server_time:{
                legend: {
                  show: false,
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

              },
            }
        }},

        methods:{
            reloadAllCharts: function() {
                this.charts = {};
                this.reloadChart('server_time');
                this.reloadChart('server_user_time');
                this.reloadChart('user_server_time');
            },
            reloadChart: function(name) {
                var data = {chart: name, time: this.time};
                if (this.user) data.user = this.user.id;
                if (this.server) data.server = this.server;

                api.post('online/chart', data)
                    .then(response => {
                      if (response.data.success){
                        if (this.options[name]){
                          response.data.chart.chartOptions = {...response.data.chart.chartOptions, ...this.options[name]};
                        }

                        this.$set(this.charts, name, response.data.chart);
                      }
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

<style lang="scss">
@import '@core/scss/vue/libs/vue-flatpicker.scss';
@import '@core/scss/vue/libs/vue-select.scss';
</style>