import store from "./store/index";
import moment from "moment-timezone";

export default {
    install (Vue, options) {
        Vue.mixin({
            methods: {
                hasPermission(perm){
                    return store.getters.userPermissions.includes(perm);
                },
                declOfNum(n, titles) {
                    return titles[(n % 10 === 1 && n % 100 !== 11) ? 0 : n % 10 >= 2 && n % 10 <= 4 && (n % 100 < 10 || n % 100 >= 20) ? 1 : 2]
                },
                getHeadUrl(uuid){
                    return 'https://s3.streamcraft.net/heads/' + uuid + '.png';
                },
                humanDiff(date){
                    var str = [];

                    var diffDuration = moment.duration(moment().diff(moment.tz(date, 'Europe/Moscow')));

                    diffDuration.years() > 0 ? str.push(diffDuration.years() + ' ' + this.declOfNum(diffDuration.years(), ['год', 'года', 'лет'])) : null;

                    diffDuration.months() > 0 ? str.push(diffDuration.months() + ' ' + this.declOfNum(diffDuration.months(), ['месяц', 'месяца', 'месяцев'])) : null;

                    diffDuration.years() <= 0 &&
                    diffDuration.months() <= 0 &&
                    diffDuration.days() > 0 ?
                        str.push(diffDuration.days() + ' ' + this.declOfNum(diffDuration.days(), ['день', 'дня', 'дней'])) : null;

                    diffDuration.years() <= 0 &&
                    diffDuration.months() <= 0 &&
                    diffDuration.days() <= 0 &&
                    diffDuration.hours() > 0 ?
                        str.push(diffDuration.hours() + ' ' + this.declOfNum(diffDuration.hours(), ['час', 'часа', 'часов'])) : null;

                    diffDuration.years() <= 0 &&
                    diffDuration.months() <= 0 &&
                    diffDuration.days() <= 0 &&
                    diffDuration.hours() <= 0 &&
                    diffDuration.minutes() > 0 ?
                        str.push(diffDuration.minutes() + ' ' + this.declOfNum(diffDuration.minutes(), ['минута', 'минуты', 'минут'])) : null;

                    return str.join(' ');
                },
                readableNum(n, separator = ' '){
                    let a = n.toString().split('').reverse();
                    a.forEach(function(item, index, array){
                        if((index + 1) % 3 === 0) array[index] = separator + item;
                    });
                    return a.reverse().join('');
                },
                formatUnix(timestamp) {
                    return moment.unix(timestamp).format('lll');
                },
            }
        })
    }
}
