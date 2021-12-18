<template>
  <section id="dashboard-analytics">
    <b-row class="match-height">
      <b-col
          sm="12"
      >
        <b-card
            no-body
            class="card-statistics"
        >
          <b-card-header>
            <b-card-title>Сегодня</b-card-title>
          </b-card-header>
          <b-card-body class="statistics-body">
            <b-row>
              <b-col
                  v-for="item in statisticsItems"
                  :key="item.icon"
                  md="3"
                  sm="6"
                  class="mb-2 mb-md-0"
                  :class="item.customClass"
              >
                <b-media no-body>
                  <b-media-aside

                      class="mr-2"
                  >
                    <b-avatar
                        size="48"
                        :variant="item.color"
                    >
                      <feather-icon
                          size="24"
                          :icon="item.icon"
                      />
                    </b-avatar>
                  </b-media-aside>
                  <b-media-body class="my-auto">
                    <h4 class="font-weight-bolder mb-0">
                      {{ item.title }}
                    </h4>
                    <b-card-text class="font-small-3 mb-0">
                      {{ item.subtitle }}
                    </b-card-text>
                  </b-media-body>
                </b-media>
              </b-col>
            </b-row>
          </b-card-body>
        </b-card>
      </b-col>
    </b-row>
    <b-row class="match-height">
      <b-col sm="12">
        <b-card
            v-if="charts.donate"
            no-body
            class="card-revenue-budget"
        >
          <b-row class="mx-0">
            <b-col
                md="8"
                class="revenue-report-wrapper"
            >
              <div class="d-sm-flex justify-content-between align-items-center mb-3">
                <h4 class="card-title mb-50 mb-sm-0">
                  Пополнения
                </h4>
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
                  <div class="d-flex align-items-center mr-2">
                    <span class="bullet bullet-primary svg-font-small-3 mr-50 cursor-pointer"/>
                    <span>Сумма</span>
                  </div>
                </div>
              </div>

              <!-- chart -->
              <vue-apex-charts
                  id="revenue-report-chart"
                  type="bar"
                  height="230"
                  :options="charts.donate.chartOptions"
                  :series="charts.donate.series"
              />
            </b-col>
            <b-col
                md="4"
                class="budget-wrapper"
            >
              <h2 class="mb-3">
                Сегодня: {{ readableNum(charts.donate.today) }} руб.
              </h2>
              <div class="text-center">
                <span class="font-weight-bolder mr-25">Этот период:</span>
                <span>{{ readableNum(charts.donate.this_period) }} руб.</span>
                <br>
                <br>
                <span class="font-weight-bolder mr-25">Прошлый период:</span>
                <span>{{ readableNum(charts.donate.last_period) }} руб.</span>
              </div>
            </b-col>
          </b-row>
        </b-card>

      </b-col>
    </b-row>
    <b-row class="match-height">
      <b-col sm="12" md="6">
        <b-card no-body v-if="charts.branch_profit">
          <b-card-header>
            <b-card-title class="mb-50">
              Доход по веткам
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
                :options="charts.branch_profit.chartOptions"
                :series="charts.branch_profit.series"
            />
          </b-card-body>
        </b-card>
      </b-col>
      <b-col sm="12" md="6">
        <b-card no-body v-if="charts.server_profit">
          <b-card-header>
            <b-card-title class="mb-50">
              Доход по серверам
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
                :options="charts.server_profit.chartOptions"
                :series="charts.server_profit.series"
            />
          </b-card-body>
        </b-card>
      </b-col>
    </b-row>
    <b-row class="match-height">
      <b-col sm="12" md="6">
        <b-card
            v-if="topBuyTable"
            no-body
            class="card-company-table"
        >
          <b-table
              :items="topBuyTable"
              responsive
              :fields="topBuyFields"
              class="mb-0"
          >
            <template #cell(name)="data">
              <div class="d-flex align-items-center">
                <b-avatar
                    rounded
                    size="64"
                    variant="light-company"
                >
                  <b-img
                      :src="data.item.icon"
                      alt="avatar img"
                  /></b-avatar>
                <div>
                  <div class="font-weight-bolder">
                    {{ data.item.name }}
                  </div>
                  <div class="font-small-2 text-muted">
                    {{ data.item.type }}
                  </div>
                </div>
              </div>
            </template>

            <!-- views -->
            <template #cell(buys)="data">
              <div class="d-flex flex-column">
                <span class="font-weight-bolder mb-25">{{ data.item.count }}</span>
                <span class="font-small-2 text-muted text-nowrap">за выбранные даты</span>
              </div>
            </template>

            <!-- revenue -->
            <template #cell(revenue)="data">
              {{ readableNum(data.item.sum) }} руб.
            </template>

          </b-table>
        </b-card>
      </b-col>
      <b-col sm="12" md="6">
        <b-card no-body v-if="charts.shop_buys">
          <b-card-header>
            <b-card-title class="mb-50">
              Покупок предметов
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
                :options="charts.shop_buys.chartOptions"
                :series="charts.shop_buys.series"
            />
          </b-card-body>
        </b-card>
      </b-col>
    </b-row>
    <b-row class="match-height">
      <b-col sm="12" md="8">
        <b-card no-body v-if="charts.api_buys">
          <b-card-header>
            <b-card-title class="mb-50">
              Списания API
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
                :options="charts.api_buys.chartOptions"
                :series="charts.api_buys.series"
            />
          </b-card-body>
        </b-card>
      </b-col>
      <b-col sm="12" md="4">
        <b-card no-body v-if="charts.pay_systems">
          <b-card-header>
            <b-card-title class="mb-50">
              Платежные системы
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
                type="donut"
                height="400"
                :options="charts.pay_systems.chartOptions"
                :series="charts.pay_systems.series"
            />
          </b-card-body>
        </b-card>
      </b-col>
    </b-row>
    <b-row class="match-height">
      <b-col sm="12" md="4">
        <b-card no-body v-if="charts.groups_active">
          <b-card-header>
            <b-card-title class="mb-50">
              Активные группы
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
                type="donut"
                height="400"
                :options="charts.groups_active.chartOptions"
                :series="charts.groups_active.series"
            />
          </b-card-body>
        </b-card>
      </b-col>
      <b-col sm="12" md="8">
        <b-card no-body v-if="charts.group_buys">
          <b-card-header>
            <b-card-title class="mb-50">
              Покупок групп
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
                :options="charts.group_buys.chartOptions"
                :series="charts.group_buys.series"
            />
          </b-card-body>
        </b-card>
      </b-col>
    </b-row>
  </section>
</template>

<script>
import {
  BRow, BCol, BCard, BCardHeader, BCardBody, BCardSubTitle, BCardTitle,
  BCardText, BMedia, BMediaAside, BAvatar, BMediaBody, BTable, BImg
} from 'bootstrap-vue'

import StatisticCardWithAreaChart from '@core/components/statistics-cards/StatisticCardWithAreaChart.vue'

import VuePerfectScrollbar from 'vue-perfect-scrollbar'
import VueApexCharts from 'vue-apexcharts'
import {$themeColors} from '@themeConfig'
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
    BCol, BRow, BCard, BCardHeader, BCardBody, BCardSubTitle, BCardTitle, BTable,  BImg,
    BCardText, BMedia, BMediaAside, BAvatar, BMediaBody,
    VueApexCharts,
    VuePerfectScrollbar,
    StatisticCardWithAreaChart
  },
  created: function () {
    this.loadCharts();
    this.loadStats();
    this.loadTopBuys();
  },
  methods: {
    loadTopBuys(){
      api.post("dashboard/profit/top_buys",{
        range: this.rangePicker && this.rangePicker.includes('—') ? this.rangePicker : null
      }).then(response => {
        if (response.data.success) {
          this.topBuyTable = response.data.data;
        }
      });
    },
    loadStats() {
      api.post("dashboard/profit/stats").then(response => {
        if (response.data.success) {
          this.statisticsItems = response.data.stats;
        }
      })
    },
    loadCharts() {
      var charts = ['group_buys', 'shop_buys', 'api_buys', 'donate', 'pay_systems', 'profit_year', 'groups', 'groups_active', 'server_profit', 'branch_profit'];

      charts.forEach(chart => {
        this.$set(this.charts, chart, null);

        api.post("dashboard/chart", {
          chart: chart,
          range: this.rangePicker && this.rangePicker.includes('—') ? this.rangePicker : null
        }).then(response => {
          if (response.data.success) {
            if (this.options[chart]) {
              response.data.chart.chartOptions = {...response.data.chart.chartOptions, ...this.options[chart]};
            }

            this.$set(this.charts, chart, response.data.chart);
          }
        })
      });
    },
    openLink(link) {
      window.open(link, "_blank");
    }
  },
  data() {
    return {
      topBuyTable: [],
      statisticsItems: [],

      topBuyFields: [
        { key: 'name', label: 'ПРЕДМЕТ' },
        { key: 'buys', label: 'ПОКУПОК' },
        { key: 'revenue', label: 'ДОХОД' },
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

      options: {
        branch_profit:{
          chart: {
            stacked: true,
            toolbar: {show: false},
          },
          dataLabels: {
            enabled: false,
          },
          colors: [$themeColors.success]
        },
        server_profit:{
          chart: {
            stacked: true,
            toolbar: {show: false},
          },
          dataLabels: {
            enabled: false,
          },
          colors: [$themeColors.info]
        },
        donate: {
          chart: {
            stacked: true,
            type: 'bar',
            toolbar: {show: false},
          },
          grid: {
            padding: {
              top: -20,
              bottom: -10,
            },
            yaxis: {
              lines: {show: true},
            },
          },
          xaxis: {
            labels: {
              style: {
                colors: '#6E6B7B',
                fontSize: '0.86rem',
                fontFamily: 'Montserrat',
              },
            },
            axisTicks: {
              show: false,
            },
            axisBorder: {
              show: false,
            },
          },
          legend: {
            show: false,
          },
          dataLabels: {
            enabled: false,
          },
          colors: [$themeColors.primary, $themeColors.warning],
          plotOptions: {
            bar: {
              columnWidth: '25%',
              endingShape: 'rounded',
            },
            distributed: true,
          },
          yaxis: {
            labels: {
              style: {
                colors: '#6E6B7B',
                fontSize: '0.86rem',
                fontFamily: 'Montserrat',
              },
            },
          },
        },
        pay_systems: {
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

        },
        groups_active: {
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

        },
      }
    };
  },
  watch: {
    rangePicker: function (newVal) {
      this.loadCharts();
      this.loadTopBuys();
    }
  }
};
</script>

<style lang="scss">
@import '@core/scss/vue/libs/vue-flatpicker.scss';
@import '@core/scss/vue/libs/chart-apex.scss';
@import '~@core/scss/base/bootstrap-extended/include';
@import '~@core/scss/base/components/variables-dark';

.card-company-table ::v-deep td .b-avatar.badge-light-company {
  .dark-layout & {
    background: $theme-dark-body-bg !important;
  }
}
</style>