<template>
    <input style="width: 100%" type="text" :id="id" :name="id" />
</template>

<script>
    import * as moment from 'moment';
    import * as daterangepicker from 'bootstrap-daterangepicker';

    export default {
        name: "DateRangePicker",
        props:{
            id: {
                type: String,
                default: 'datarangepicker'
            },
            time: {
                type: Boolean,
                default: false
            },
            format: {
                type: String,
                default: 'DD/MM/YYYY h:mm A'
            },
            start: {
                type: Object,
                default: function () {
                    return moment().subtract(14, 'days').startOf('day');
                }
            },
            end: {
                type: Object,
                default: function () {
                    return moment();
                }
            }
        },
        mounted() {
            console.log("DataRangePicker mounted: " + this.id);
            var self = this;
            var input = $('input#'+ this.id);
            input.daterangepicker(
                {
                    autoApply: true,
                    locale: {
                        "format": this.format,
                        "separator": " - ",
                        "applyLabel": "Выбрать",
                        "cancelLabel": "Отмена",
                        "fromLabel": "Начало",
                        "toLabel": "Конец",
                        "customRangeLabel": "Другое",
                        "weekLabel": "W",
                        "daysOfWeek": [
                            "Вс",
                            "Пн",
                            "Вт",
                            "Ср",
                            "Чт",
                            "Пт",
                            "Сб"
                        ],
                        "monthNames": [
                            "Январь",
                            "Февраль",
                            "Март",
                            "Апрель",
                            "Май",
                            "Июнь",
                            "Июль",
                            "Август",
                            "Сентябрь",
                            "Октябрь",
                            "Ноябрь",
                            "Декабрь"
                        ],
                        "firstDay": 1
                    },
                    ranges: {
                        'Сегодня': [moment().startOf('day'), moment().endOf('day')],
                        'Вчера': [moment().subtract(1, 'days').startOf('day'), moment().subtract(1, 'days').endOf('day')],
                        'Последние 7 дней': [moment().subtract(6, 'days').startOf('day'), moment()],
                        'Следующие 7 дней': [moment(), moment().add(6, 'days')],
                        'Последние 30 дней': [moment().subtract(29, 'days').startOf('day'), moment()],
                        'Следующие 30 дней': [moment(), moment().add(29, 'days')],
                        'Этот месяц': [moment().startOf('month').startOf('day'), moment().endOf('month').endOf('day')],
                        'Прошлый месяц': [moment().subtract(1, 'month').startOf('month').startOf('day'), moment().subtract(1, 'month').endOf('month').endOf('day')]
                    },
                    startDate: this.start,
                    endDate: this.end,
                    opens: "center",
                    timePicker: this.time,
                    format: this.format
                }
            );

            input.on('apply.daterangepicker', function(ev, picker) {
                console.log("DataRangePicker apply: " + this.id);

                console.log(picker);

                //self.$emit('changed', picker);
                self.$emit('input', {
                    start: picker.startDate,
                    end: picker.endDate
                })
            });
        }
    }
</script>

<style scoped>

</style>