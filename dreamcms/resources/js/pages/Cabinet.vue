<template>
    <div class="inner cabinet">
        <div class="headline">
            <div class="row">
                <div class="col-12 col-lg-8 col-xl-7">
                    <h2>Привет, {{ user.login }}!</h2>
                    <div class="section">
                        <div class="user_about">
                            <div class="user_pic" :style="'background-image:url(' + getHeadUrl(user.uuid) + ')'"></div>
                            <ul class="user_meta">
                                <li>
                                    <span>Почта</span>
                                    <span>{{ user.email }}</span>
                                </li>
                                <li v-html="'<span>Действующая группа</span>' + role">
                                </li>
                                <li>
                                    <span>Регистрация аккаунта</span>
                                    <span>{{ moment.unix(user.reg_time).format('lll') }}</span>
                                </li>
                                <li>
                                    <span>Последняя активность</span>
                                    <span>{{ moment.unix(user.last_play).format('lll') }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="user_help">
                            <router-link :to="{name: 'user', params: {login: user.login}}">Профиль форума</router-link>
                            <router-link :to="{name: 'shop'}">Магазин блоков</router-link>
                            <a href="#" @click.prevent="modals.stuck = true">Я застрял!</a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4 col-xl-5">
                    <div class="row balance mx-0 mt-4 mt-lg-0">
                        <a href="#" class="col-12 col-md col-lg-12 streams" @click.prevent="openModal('balance')">
                            <h4>{{ readableNum(Math.round(user.realmoney)) }}<small>СТРИМОВ</small></h4>
                            <p>Нажмите сюда и пополните Ваш счёт всего в несколько кликов мыши!</p>
                        </a>
                        <a href="#" class="col-12 col-md col-lg-12 mt-4 mt-md-0 ml-0 ml-md-4 ml-lg-0 mt-lg-4 coins" @click.prevent="openModal('exchange')">
                            <h4>Пополнить коины</h4>
                            <p>Пополните баланс коинов и станьте лучшим на любом из серверов!</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="body">
            <ul class="cabinet_tabs">
                <li :class="this.$route.name === 'cabinet' ? 'checked' : ''">
                    <router-link :to="{name: 'cabinet'}">Ваш персонаж</router-link>
                </li>
                <li :class="this.$route.name === 'donate' ? 'checked' : ''">
                    <router-link :to="{name: 'donate'}">Привилегии</router-link>
                </li>
                <li :class="this.$route.name === 'kits' ? 'checked' : ''">
                    <router-link :to="{name: 'kits'}">Наборы</router-link>
                </li>
                <li :class="this.$route.name === 'profile' ? 'checked' : ''">
                    <router-link :to="{name: 'profile'}">Профиль</router-link>
                </li>
                <li :class="this.$route.name === 'security' ? 'checked' : ''">
                    <router-link :to="{name: 'security'}">Безопасность</router-link>
                </li>
                <li :class="this.$route.name === 'punish' ? 'checked' : ''">
                    <router-link :to="{name: 'punish'}">Наказания</router-link>
                </li>
            </ul>
            <div class="cabinet_content">
                <router-view></router-view>
            </div>
        </div>

        <b-modal v-model="modals.stuck" modal-class="modal" hide-header hide-footer content-class="custom_modal" size="lg">
            <div id="modal">
                <div class="window tpSpawn">
                    <div class="header">
                        <h2>Я застрял!</h2>
                        <p>Попали в ловушку и не можете выбраться самостоятельно? Выберите сервер и моментально телепортируйтесь на спавн по кнопке ниже!</p>
                        <div class="row justify-content-center mt-3">
                            <div class="col-12 col-sm-6 col-md-5 px-2 mb-2 mb-sm-0">
                                <v-select class="btn_common select mt-2" label="name" :filterable="false" :options="Object.values(this.servers)" placeholder="Выберите сервер" v-model="modals.server"></v-select>
                            </div>
                            <a href="#" class="btn_common primary" @click.prevent="stp" v-if="modals.server != null">Телепортировать</a>
                        </div>
                    </div>
                </div>
            </div>
        </b-modal>
    </div>
</template>

<script>
    import api from '../api';
    import {mapGetters} from "vuex";
    import moment from "moment";

    export default {
        name: "Cabinet",
        computed: {
            ...mapGetters(['isLogged', 'user', 'role', 'servers'])
        },
        data(){
            return {
                modals: {
                    stuck: false,
                    server: null
                }
            }
        },
        methods:{
            stp(){
                this.$recaptcha('stp').then((token) => {
                    api.post('profile/safetp', {
                        server: this.modals.server.id,
                        captcha: token
                    });
                });
            },
            openModal(modal){
                switch (modal) {
                    case 'balance':
                        this.$store.dispatch('setBalanceModal', true);
                        break;
                    case 'exchange':
                        this.$store.dispatch('setExchangeModal', true);
                        break;
                }
            },
        }
    }
</script>