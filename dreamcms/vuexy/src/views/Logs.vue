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
                        Выбор действия
                    </b-card-title>
                    <v-select v-model="type" :options="type_options" label="title"></v-select>
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

        <b-card>
            <b-card-title>
                Логи
            </b-card-title>

            <app-timeline>
                <app-timeline-item :icon="getType(action).icon" :variant="getType(action).variant"
                                   v-for="action in actions"
                                   :key="action.id">

                    <div class="d-flex flex-sm-row flex-column flex-wrap justify-content-between mb-1 mb-sm-0">
                        <h6>{{ getType(action).title }}</h6>
                        <small class="text-muted">{{ formatUnix(action.time) }}</small>
                    </div>

                    <l-map v-if="action.ip"
                        :zoom="12"
                        :center="[action.ip.lat, action.ip.lon]"
                    >
                        <l-tile-layer url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png" />
                        <l-marker :lat-lng="[action.ip.lat, action.ip.lon]" />
                    </l-map>

                    <div class="d-flex flex-sm-row flex-column justify-content-between align-items-start mb-2">
                        <div class="mb-1 mb-sm-0" v-if="action.server">
                            <span class="text-muted mb-50 d-block">Сервер</span>
                            <span>{{ action.server.name || action.server }}</span>
                        </div>
                        <div class="mb-1 mb-sm-0" v-if="action.admin">
                            <span class="text-muted mb-50 d-block">Администратор</span>
                            <span>{{ action.admin.login || action.admin }}</span>
                        </div>
                    </div>

                    <b-button
                        v-b-toggle
                        v-ripple.400="'rgba(113, 102, 240, 0.15)'"
                        size="sm"
                        variant="outline-primary"
                        :href="'#toggle_' + action.id"
                        @click.prevent
                    >
                        Полный отчет
                    </b-button>
                    <b-collapse :id="'toggle_' + action.id">
                        <b-list-group
                            flush
                            class="mt-1"
                        >
                            <b-list-group-item
                                               href="#"
                                               class="d-flex justify-content-between align-items-center bg-transparent"
                            >
                                <span>action: {{ action.action }}</span>
                            </b-list-group-item>

                            <b-list-group-item v-for="(value, key) in action.params" :key="action.id + '_' + key"
                                href="#"
                                class="d-flex justify-content-between align-items-center bg-transparent"
                            >
                                <span>{{ key }}: {{ value }}</span>
                            </b-list-group-item>
                        </b-list-group>
                    </b-collapse>
                </app-timeline-item>
            </app-timeline>
        </b-card>
    </section>
</template>

<script>
import api from "@/api";
import moment from "moment"
import UserSelector from "../components/UserSelector";
import DateRangePicker from '../components/DateRangePicker';
import vSelect from 'vue-select';
import {Russian} from 'flatpickr/dist/l10n/ru.js';
import flatPickr from 'vue-flatpickr-component'
import {
    BCard, BRow, BCol, BSpinner, BCardTitle, BImg, BAvatar, BMedia, BButton, BCollapse, VBToggle, BListGroup, BListGroupItem, BAvatarGroup, BBadge, VBTooltip
} from 'bootstrap-vue'
import {mapGetters} from "vuex";

import AppTimeline from '@core/components/app-timeline/AppTimeline.vue'
import AppTimelineItem from '@core/components/app-timeline/AppTimelineItem.vue'
import Ripple from 'vue-ripple-directive'
import { LMap, LTileLayer, LMarker } from 'vue2-leaflet'
import { Icon } from 'leaflet'
import 'leaflet/dist/leaflet.css'

// eslint-disable-next-line no-underscore-dangle
delete Icon.Default.prototype._getIconUrl
Icon.Default.mergeOptions({
    iconRetinaUrl: require('leaflet/dist/images/marker-icon-2x.png'),
    iconUrl: require('leaflet/dist/images/marker-icon.png'),
    shadowUrl: require('leaflet/dist/images/marker-shadow.png'),
})
/* eslint-disable global-require */

export default {
    name: "Logs",
    components: {
        UserSelector, DateRangePicker,

        vSelect, flatPickr,

        LMap,
        LTileLayer,
        LMarker,

        BCard, BRow, BCol, BSpinner, BCardTitle, AppTimeline, AppTimelineItem, BImg, BAvatar, BMedia, BButton, BCollapse, BListGroup, BListGroupItem, BAvatarGroup, BBadge,
    },
    directives: { 'b-toggle': VBToggle, 'b-tooltip': VBTooltip, Ripple },
    computed: {
        ...mapGetters(['servers'])
    },
    data() {
        return {
            loading: false,

            user: null,
            time: null,
            type: null,

            rangePickerConfig: {
                mode: 'range',
                locale: Russian,
                defaultDate: [moment().subtract(31, "day").toDate(), moment().toDate()],
            },

            type_options: [
                {
                    title: 'Ошибка ввода 2FA',
                    value: '2fa_fail',
                    variant: 'danger',
                    icon: 'UserXIcon'
                },
                {
                    title: 'Списание API',
                    value: 'api_withdraw',
                    variant: 'warning',
                    icon: 'DollarSignIcon'
                },
                {
                    title: 'Пополнение баланса',
                    value: 'balance_add',
                    variant: 'success',
                    icon: 'CreditCardIcon'
                },
                {
                    title: 'Забанен на форуме',
                    value: 'banned_forum',
                    variant: 'danger',
                    icon: 'SlashIcon'
                },
                {
                    title: 'Покупка группы',
                    value: 'buygroup',
                    variant: 'success',
                    icon: 'StarIcon'
                },
                {
                    title: 'Покупка предмета',
                    value: 'buyitem',
                    variant: 'success',
                    icon: 'ShoppingBagIcon'
                },
                {
                    title: 'Покупка кита',
                    value: 'buy_kit',
                    variant: 'success',
                    icon: 'ShoppingBagIcon'
                },
                {
                    title: 'Покупка разбана',
                    value: 'buy_unban',
                    variant: 'warning',
                    icon: 'UserMinusIcon'
                },
                {
                    title: 'Получение из корзины',
                    value: 'cart_redeem',
                    variant: 'warning',
                    icon: 'MinusCircleIcon'
                },
                {
                    title: 'Смена пароля',
                    value: 'changepass',
                    variant: 'warning',
                    icon: 'FileTextIcon'
                },
                {
                    title: 'Отключение 2FA',
                    value: 'disableotp',
                    variant: 'danger',
                    icon: 'AlertCircleIcon'
                },
                {
                    title: 'Подтверждение почты',
                    value: 'email_confirm',
                    variant: 'success',
                    icon: 'AtSignIcon'
                },
                {
                    title: 'Включение 2FA',
                    value: 'enableotp',
                    variant: 'success',
                    icon: 'AwardIcon'
                },
                {
                    title: 'Подключение соц-сети',
                    value: 'integration_link',
                    variant: 'success',
                    icon: 'AwardIcon'
                },
                {
                    title: 'Восстановление через соц-сеть',
                    value: 'integration_recovery',
                    variant: 'warning',
                    icon: 'AwardIcon'
                },
                {
                    title: 'Получение из корзины',
                    value: 'item_redeem',
                    variant: 'warning',
                    icon: 'MinusCircleIcon'
                },
                {
                    title: 'Получение из корзины',
                    value: 'received_items',
                    variant: 'warning',
                    icon: 'MinusCircleIcon'
                },
                {
                    title: 'Открытие кейса',
                    value: 'open_case',
                    variant: 'success',
                    icon: 'BoxIcon'
                },
                {
                    title: 'Восстановление пароля',
                    value: 'reset_password',
                    variant: 'danger',
                    icon: 'MailIcon'
                },
                {
                    title: 'Покупка коинов на сервере',
                    value: 'sendserver',
                    variant: 'success',
                    icon: 'SendIcon'
                },
                {
                    title: 'Установка префикса',
                    value: 'setprefix',
                    variant: 'warning',
                    icon: 'TypeIcon'
                },
                {
                    title: 'Установка префикса',
                    value: 'setprefix',
                    variant: 'warning',
                    icon: 'TypeIcon'
                },
            ],

            default_type: {
                title: 'Неизвестное действие',
                value: '',
                variant: 'danger',
                icon: 'XIcon'

            },

            actions: []
        }
    },
    methods: {
        getType(action){
            var type = this.type_options.find(typeact => typeact.value === action.action);
            return type ? type : this.default_type;
        },
        loadData() {
            this.loading = true;

            api.post("logs/user", {
                user: this.user ? this.user.id : null,
                type: this.type ? this.type.value : null,
                range: this.time
            }).then(response => {
                this.actions = response.data.actions;
                this.loading = false;
            });
        }
    },
    watch: {
        time: function () {
            this.loadData();
        },
        type: function () {
            this.loadData();
        },
        user: function () {
            this.loadData();
        }
    }
}
</script>

<style lang="scss">
.vue2leaflet-map{
    &.leaflet-container{
        height: 350px;
    }
}
@import '@core/scss/vue/libs/vue-flatpicker.scss';
@import '@core/scss/vue/libs/vue-select.scss';
</style>