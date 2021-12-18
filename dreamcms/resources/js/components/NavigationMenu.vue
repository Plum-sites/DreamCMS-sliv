<template>
    <nav>
        <div class="wrapper">
            <div class="nav_menu">
                <router-link :to="{name: 'news'}" class="logo"></router-link>
                <ul class="d-none d-lg-block">
                    <li v-for="item in menu" :class="(item.cols || item.child ? 'dropdown' : (item.class ? item.class : ''))">
                        <router-link v-if="item.to" :to="item.to" class="link">{{ item.title }}</router-link>

                        <div v-if="item.cols">
                            <span class="link">{{ item.title }}</span>

                            <div class="dropdown_container wide_wrap">
                                <div class="dropdown_menu">
                                    <div class="row">
                                        <div class="col" v-for="col in item.cols">
                                            <ul v-for="subcol in col">
                                                <li>
                                                    <h4>{{ subcol.title }}</h4>
                                                </li>
                                                <li v-for="child in subcol.child">
                                                    <router-link :to="child.to">{{ child.title }}</router-link>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="item.child">
                            <span class="link">{{ item.title }}</span>

                            <div class="dropdown_container">
                                <div class="dropdown_menu">
                                    <ul>
                                        <li v-for="child in item.child">
                                            <router-link :to="child.to">{{ child.title }}</router-link>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
                <ul class="d-none d-lg-block" v-if="!this.isLogged">
                    <li class="d-lg-none d-xl-inline-block">
                        <a href="#" id="goToLogin" class="link" @click.prevent="openLoginModal">Вход на сайт</a>
                    </li>
                    <li>
                        <a href="#" id="goToReg" class="btn_common primary" @click.prevent="openRegisterModal">Регистрация</a>
                    </li>
                </ul>
                <ul class="d-none d-lg-block profile" v-else>
                    <li>
                        <router-link class="link" :to="{name: 'cabinet'}">
                            <div class="user_pic">
                                <img :src="getHeadUrl(user.uuid)">
                            </div>
                            {{ user.login }}
                        </router-link>
                    </li>
                    <li class="notice">
                        <a href="#" class="link">
                            <i class="far fa-envelope"></i>
                        </a>
                    </li>
                    <li :class="'notice ' + (unreadNotifications > 0 ? 'unread' : '')">
                        <router-link :to="{name: 'notifications'}" class="link">
                            <i class="far fa-bell"></i>
                        </router-link>
                    </li>
                    <li>
                        <a href="#" class="link" @click.prevent="logout">Выход</a>
                    </li>
                </ul>
                <ul class="d-block d-lg-none text-right">
                    <a href="#" class="mobile_menu_toggle" @click.prevent="mobileMenu = true">
                        <span class="bars"></span>
                    </a>
                </ul>
            </div>
            <div :class="'mobile_menu d-block d-lg-none ' + (mobileMenu === true ? 'check' : '')">
                <div class="popup">
                    <div class="row align-items-center m-0">
                        <h3>Меню</h3>
                        <div class="col dotted"></div>
                        <a href="#" class="mobile_menu_toggle" @click.prevent="mobileMenu = false">
                            <i class="fal fa-times"></i>
                        </a>
                    </div>
                    <div class="panel" v-if="this.isLogged">
                        <ul class="row profile light mx-0">
                            <li>
                                <router-link class="link" :to="{name: 'cabinet'}">
                                    <div class="user_pic">
                                        <img :src="getHeadUrl(user.uuid)">
                                    </div>
                                    {{ user.login }}
                                </router-link>
                            </li>
                            <li class="notice">
                                <a href="#" class="link">
                                    <i class="far fa-envelope"></i>
                                </a>
                            </li>
                            <li :class="'notice ' + (unreadNotifications > 0 ? 'unread' : '')">
                                <router-link :to="{name: 'notifications'}" class="link">
                                    <i class="far fa-bell"></i>
                                </router-link>
                            </li>
                            <li class="ml-auto">
                                <a href="#" class="link" @click.prevent="logout">Выход</a>
                            </li>
                        </ul>
                    </div>
                    <div class="panel" v-else>
                        <a href="#" class="btn_common dark" @click.prevent="openRegisterModal">Регистрация</a>
                        <a href="#" class="btn_common float-right" @click.prevent="openLoginModal">Войти</a>
                    </div>

                    <div class="row area">
                        <div class="col-12 col-sm-6">
                            <h4>Навигация</h4>
                            <ul>
                                <li v-for="item in menu" v-if="item.to">
                                    <router-link :to="item.to">{{ item.title }}</router-link>
                                </li>
                            </ul>
                        </div>
                        <div class="col-12 col-sm" v-for="item in menu" v-if="item.child">
                            <h4>{{ item.title }}</h4>
                            <ul>
                                <li v-for="link in item.child">
                                    <router-link :to="link.to">{{ link.title }}</router-link>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="row area mt-4" v-for="item in menu" v-if="item.cols">
                        <div class="col-12 col-sm-6" v-for="col in item.cols">
                            <ul v-for="subcol in col">
                                <h4>{{ subcol.title }}</h4>
                                <li v-for="child in subcol.child">
                                    <router-link :to="child.to">{{ child.title }}</router-link>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>

<script>
    import {mapActions, mapGetters} from 'vuex';
    import {AUTH_LOGOUT} from "../api";

    export default {
        name: "NavigationMenu",
        computed:{
            ...mapGetters(['isLogged', 'menu', 'user', 'unreadNotifications'])
        },
        methods:{
            openLoginModal(){
                this.$store.dispatch('setLoginModal', true);
            },
            openRegisterModal(){
                this.$store.dispatch('setRegisterModal', true);
            },
            logout(){
                this.$store.dispatch(AUTH_LOGOUT, true);
            }
        },
        data(){
            return {
                mobileMenu: false,
            }
        },
        watch:{
            '$route': function () {
                this.mobileMenu = false;
            }
        }
    }
</script>