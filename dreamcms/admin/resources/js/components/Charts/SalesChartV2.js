// Sales Widget
import { Bar } from 'vue-chartjs'
import { ChartConfig } from "Constants/chart-config";

export default ({
   extends: Bar,
   props: ['data1', 'data2', 'labels'],
   data: function () {
      return {
         options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
               yAxes: [{
                  stacked: true,
                  ticks: {
                     display: true
                  },
                  gridLines: {
                     display: false,
                     drawBorder: false
                  }
               }],
               xAxes: [{
                  stacked: true,
                  ticks: {
                     padding: 10
                  },
                  barPercentage: 0.4,
                  categoryPercentage: 1.3,
                  gridLines: {
                     display: false,
                     drawBorder: false
                  }
               }]
            },
            legend: {
               display: false
            }
         }
      }
   },
   mounted() {
      const { labels, data1, data2 } = this;

      this.renderChart({
         labels,
         datasets: [
            {
               type: 'bar',
               label: 'Текущий период',
               backgroundColor: ChartConfig.color.primary,
               hoverBackgroundColor: ChartConfig.color.primary,
               borderWidth: 0,
               data: data1
            },
            {
               type: 'bar',
               label: 'Прошлый период',
               backgroundColor: ChartConfig.lineChartAxesColor,
               hoverBackgroundColor: ChartConfig.lineChartAxesColor,
               borderWidth: 0,
               data: data2
            }
         ]
      }, this.options)
   }
})
