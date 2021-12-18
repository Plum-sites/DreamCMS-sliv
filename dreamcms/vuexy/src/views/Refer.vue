<template>
  <section>
    <b-row class="match-height">
      <b-col sm="12" md="6">
        <user-selector v-model="user"></user-selector>
      </b-col>
    </b-row>

    <b-row class="match-height">
      <b-col sm="12" md="6">
        <b-card no-body v-if="charts.donate">
          <b-card-header>
            <b-card-title class="mb-50">
              Доходность
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
                :options="charts.donate.chartOptions"
                :series="charts.donate.series"
            />
          </b-card-body>
        </b-card>
      </b-col>
    </b-row>
  </section>
</template>

<script>
    import api from "../api";
    import DateRangePicker from "../components/DateRangePicker"
    import moment from "moment"
    import UserSelector from "../components/UserSelector";
    import VueApexCharts from 'vue-apexcharts'
    import { BRow, BCol, BCard, BCardHeader, BCardBody, BCardSubTitle, BCardTitle, BCardText} from 'bootstrap-vue'
    import flatPickr from 'vue-flatpickr-component'
    import {Russian} from 'flatpickr/dist/l10n/ru.js';

    export default {
        components: {
            flatPickr,
            VueApexCharts,
            DateRangePicker,
            UserSelector,
            BRow, BCol, BCard, BCardHeader, BCardBody, BCardSubTitle, BCardTitle, BCardText
        },
        methods:{
            loadData() {
                this.loadChart('donate');
            },
            loadChart(chart){
                console.log('Load chart: ' + chart);

                api.post("refer/chart", {
                    chart: chart,
                    user: this.user.id,
                    range: this.rangePicker && this.rangePicker.includes('—') ? this.rangePicker : null
                })
                .then(response => {
                    if (response.data.success){
                        if (this.options[chart]){
                          response.data.chart.chartOptions = {...response.data.chart.chartOptions, ...this.options[chart]};
                        }

                        this.$set(this.charts, chart, response.data.chart);
                    }
                });
            }
        },
        data() {
            return {
                rangePicker: null,
                rangePickerConfig: {
                  mode: 'range',
                  //dateFormat: 'd-m-Y',
                  locale: Russian,
                  maxDate: "today",

                  defaultDate: [moment().subtract(2, "week").toDate(), moment().toDate()],
                },

                charts: {},
                options: {},

                user: false
            };
        },
        watch:{
            rangePicker: function (val) {
                this.loadData();
            },
            user: function (newval){
                this.loadData();
            }
        }
    };
</script>
