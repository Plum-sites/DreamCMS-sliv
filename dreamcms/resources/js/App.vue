<template>
    <loader v-if="!this.isLoaded"></loader>
    <div id="app" :class="this.$route.name === 'news' ? 'index' : ''" v-else>
        <notifications></notifications>
        <modals></modals>
        <navigation-menu></navigation-menu>

        <header v-if="this.$route.name === 'news'">
            <div class="wrapper">
                <div class="row align-items-center">
                    <div class="col-12 col-xl-6 text-center text-xl-left">
                        <div v-if="this.isLogged">
                            <h1>Добро пожаловать, дорогой игрок, рады видеть тебя снова!</h1>
                            <p>Следи за обновлениями в группе ВКонтакте и на сайте, а также общайтесь в нашем Discord!</p>
                        </div>
                        <div v-else>
                            <h1>Добро пожаловать, присоединяйся к нам прямо сейчас!</h1>
                            <p>Регистрируйся, качай наш лаунчер всего в несколько кликов и бегом в игру!</p>
                        </div>
                    </div>
                    <div class="col text-right d-none d-xl-block text-right">
                        <div class="row m-0 justify-content-end">
                            <div id="vk_groups" class="mr-3"></div>
                            <iframe src="https://discordapp.com/widget?id=265467799982440448&theme=dark" width="285" height="403" allowtransparency="true" frameborder="0"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <router-view></router-view>

        <footer>
            <div class="promo_section">
                <div class="wrapper">
                    <div class="row align-items-center">
                        <div class="col-12 col-lg-5 col-xl-6 text-center text-lg-left mr-xl-5">
                            <h2>Набор модераторов</h2>
                            <p>Нами был запущен набор модераторов на все сервера, оформите Вашу заявку и присоединяйтесь к нам!</p>
                            <!--<h2>Получить ранний доступ</h2>
                            <p>Скоро нами будет запущено закрытое бета-тестирование, оформите ранний доступ на него всего в два клика!</p>-->
                        </div>
                        <div class="col-12 col-lg-7 col-xl mt-5 mt-lg-0 ml-xl-5">
                            <router-link class="row promo_box align-items-center text-center text-sm-left" :to="{name: 'moderentry'}">
                                <div class="col-12 col-sm-7">
                                    <h3>Ждём Ваших заявок!</h3>
                                    <p>Примкните к стражам порядка на любимом сервере прямо сейчас!</p>
                                </div>
                                <div class="col-12 col-sm mt-3 mt-sm-0 text-sm-right">
                                    <div class="alt_button">Продолжить</div>
                                </div>
                            </router-link>
                        </div>
                    </div>
                </div>
            </div>
            <div class="footer_section">
                <div class="wrapper">
                    <div class="row align-items-top text-center text-lg-left">
                        <div class="col-12 col-lg-3 col-xl-2 mr-xl-5">
                            <a href="#" class="logo"></a>
                        </div>
                        <div class="col my-4 my-lg-0">
                            <router-link class="link" :to="{name: 'page', params: {name: 'team'}}">Команда проекта</router-link>
                            <router-link class="link" :to="{name: 'page', params: {name: 'team'}}">Обратная связь</router-link>

                            <a href="https://vk.com/vk" class="link" target="_blank">Мы ВКонтакте</a>
                            <a href="https://discordapp.com/invite/SDxzbAc" class="link" target="_blank">Наш Discord</a>
                            <div class="sub_line">
                                <p>
                                    <router-link :to="{name: 'forum'}">Форум</router-link>

                                    <router-link :to="{name: 'page', params: {name: 'rules'}}">Правила</router-link>
                                    <router-link :to="{name: 'page', params: {name: 'commands'}}">Команды</router-link>
                                    <router-link :to="{name: 'banlist'}">Банлист</router-link>
                                    <router-link :to="{name: 'page', params: {name: 'download'}}">Скачать лаунчер</router-link>

                                    <a href="https://vk.com/im?sel=-120127301" target="_blank">Техническая поддержка</a>
                                </p>
                                <p>© DreamCMS 2011 - {{ moment().format('Y') }}. <span class="d-block d-sm-inline">Разработано знатоками своего дела с любовью <i style="color: red;" class="fal fa-heart"></i></span></p>
                            </div>
                        </div>
                        <div class="col-12 col-lg-2 text-lg-right">
                            <a href="#" class="banner">
                                <img src="/assets/img/metrika.png" width="88" height="31">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<script>
    import { LOAD_USER } from "./api";
    import { mapGetters } from 'vuex';

    import * as moment from "moment";

    import NavigationMenu from "./components/NavigationMenu";
    import Loader from "./components/Loader";

    import Modals from "./Modals";
    import debounce from 'lodash/debounce'

    export default {
        components: {Modals, Loader, NavigationMenu},
        data() {
            return {
                vkInitialized: true
            }
        },
        created(){
            this.$store.dispatch(LOAD_USER);
        },
        computed: {
            ...mapGetters(['isLogged', 'isLoaded'])
        },
        methods:{
            initVK(){
                this.$nextTick(function () {
                    if (!this.vkInitialized){
                        VK.Widgets.Group("vk_groups", {mode: 4, no_cover: 1,  width: "285", height: "400", color1: '353535', color2: 'FFF', color3: 'a6a6a6'}, 120127301);
                    }
                });
            }
        },
        updated: function () {
            if (this.$route.name === 'news'){
                this.initVK();
            }
        },
        watch:{
            '$route': function (from, to) {
                if ((to && to.name === 'news') || (from && from.name === 'news')){
                    this.vkInitialized = false;
                }
            }
        }
    };
</script>
