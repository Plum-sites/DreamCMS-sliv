// Sales Widget
import { Bar, Line } from 'vue-chartjs'
import { ChartConfig } from "Constants/chart-config";

export default ({
    extends: Bar,
    Line,
    props: ['labels', 'datasets'],
    data: function () {
        return {
            options: {
                responsive: true,
                maintainAspectRatio: true,
                scales: {
                    yAxes: [{
                        ticks: {
                            display: false,
                        },
                        gridLines: {
                            display: false,
                            drawBorder: false
                        }
                    }],
                    xAxes: [{
                        ticks: {
                            padding: 0
                        },
                        gridLines: {
                            display: false,
                            drawBorder: false
                        }
                    }]
                },
                legend: {
                    display: true
                }
            }
        }
    },
    mounted() {
        const { labels, datasets } = this;
        if (this.enableShadow !== false) {
            let ctx = this.$refs.canvas.getContext('2d')
            let _stroke = ctx.stroke
            ctx.stroke = function () {
                ctx.save()
                ctx.shadowColor = ChartConfig.shadowColor
                ctx.shadowBlur = 10
                ctx.shadowOffsetX = 0
                ctx.shadowOffsetY = 12
                _stroke.apply(this, arguments)
                ctx.restore()
            }
        }

        this.renderChart({
            labels,
            datasets
        }, this.options)
    }
})
