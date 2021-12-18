// Line Chart Widget
import { Line } from 'vue-chartjs'
import { ChartConfig } from "Constants/chart-config";
import { hexToRgbA } from "Helpers/helpers";

export default {
   extends: Line,
   props: ['data', 'labels'],
   data() {
      return {
         gradient1: null,
         options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
               yAxes: [{
                  ticks: {
                     beginAtZero: true,
                     display: false
                  },
                  gridLines: {
                     display: false,
                     drawBorder: false,
                     drawTicks: false
                  }
               }],
               xAxes: [{
                  ticks: {
                     display: true,
                     beginAtZero: true
                  },
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
      const { labels, data } = this;

      let ctx = this.$refs.canvas.getContext('2d')
      let _stroke = ctx.stroke
      ctx.stroke = function () {
         ctx.save()
         ctx.shadowColor = 'rgba(0,0,0,0.5)'
         ctx.shadowBlur = 8
         ctx.shadowOffsetX = 0
         ctx.shadowOffsetY = 4
         _stroke.apply(this, arguments)
         ctx.restore()
      }

      let gradientColor = ' '
      this.gradient1 = this.$refs.canvas.getContext('2d').createLinearGradient(0, 110, 0, 0)
      this.gradient1.addColorStop(0, hexToRgbA(ChartConfig.color.white, 0.2))
      this.gradient1.addColorStop(0.5, hexToRgbA(ChartConfig.color.info, 0.2))

      this.renderChart({
         labels,
         datasets: [
            {
               label: 'Data 2',
               data: data,
               lineTension: 0.5,
               backgroundColor: this.gradient1,
               borderColor: ChartConfig.color.info,
               pointBorderWidth: 0,
               pointHoverRadius: 0,
               pointRadius: 0,
               pointHoverBorderWidth: 0,
               borderWidth: 2,
               fill: true
            }
         ]
      }, this.options);
   }
}
