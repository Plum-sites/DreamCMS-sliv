<template>
    <b-modal v-model="loginModal" modal-class="modal" hide-header hide-footer content-class="custom_modal" size="lg">
        <div id="modal">
            <div :class="'window auth ' + (this.loading ? 'unload' : '')">
                <div class="row">
                    <div class="col-12 col-sm-9">
                        <div class="header">
                            <h2>Авторизация</h2>
                            <p>Что-то пошло не так? Сообщите об этом в нашу <a target="_blank" href="https://vk.com/vk">группу ВКонтакте</a> и мы оперативно поможем Вам решить проблему!</p>
                        </div>
                        <div class="body">
                            <div class="row align-items-center">
                                <div class="col-12 col-md-5">
                                    <label>Введите логин или EMail</label>
                                </div>
                                <div class="col-12 col-sm-9 col-md mt-1 mt-md-0">
                                    <b-input id="name" class="form-control-lg" placeholder="Ваш игровой логин или почта" v-model="login"></b-input>
                                </div>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-12 col-md-5">
                                    <label>Введите пароль</label>
                                    <small class="d-inline d-md-block"><router-link :to="{name: 'recovery'}" @click.native="loginModal = false">Забыли пароль?</router-link></small>
                                </div>
                                <div class="col-12 col-sm-9 col-md mt-1 mt-md-0">
                                    <b-input class="form-control-lg" type="password" placeholder="••••••••••••••••" v-model="password"></b-input>
                                </div>
                            </div>
                            <div class="row mt-2 mt-md-3">
                                <div class="col-12 col-md-5"> </div>
                                <label class="col-12 col-sm-9 col-md">
                                    <input id="session" type="checkbox" class="checkbox" v-model="remember">
                                    <small>Запомнить вход с этого устройства</small>
                                </label>
                            </div>
                            <div class="row align-items-center" v-if="otp.enabled">
                                <div class="col-12 col-md-5">
                                    <label>Введите код из приложения или резервный код</label>
                                </div>
                                <div class="col-12 col-sm-9 col-md mt-1 mt-md-0">
                                    <b-input class="form-control-lg" type="text" placeholder="Код 2FA" v-model="otp.code"></b-input>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 col-md-5"></div>
                                <div class="col-12 col-sm-9 col-md">
                                    <a href="#" class="btn_common primary" @click.prevent="runCaptcha">Войти на сайт</a>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 col-md-5"></div>
                                <div class="col-12 col-sm-9 col-md">
                                    <vue-telegram-login
                                        mode="redirect"
                                        telegram-login="dreamcms_bot"
                                        :redirect-url="'https://' + window.location.hostname + '/oauth/login/telegram'"
                                        userpic
                                        radius="5"
                                        requestAccess="write"
                                    />
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 col-md-5">
                                    <label>Войти через:</label>
                                </div>
                                <div class="col-12 col-sm-9 col-md">
                                    <a :href="getIntegrationURL('vkontakte', 'login')" class="btn_common info" style="font-size: 28px"><i class="fab fa-vk"></i></a>
                                    <a :href="getIntegrationURL('discord', 'login')" class="btn_common info" style="font-size: 28px"><i class="fab fa-discord"></i></a>
                                    <a :href="getIntegrationURL('yandex', 'login')" class="btn_common info" style="font-size: 28px"><i class="fab fa-yandex"></i></a>
                                    <a :href="getIntegrationURL('steam', 'login')" class="btn_common info" style="font-size: 28px"><i class="fab fa-steam"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="backdrop">
                    <div class="item circle"></div>
                    <div class="item circle"></div>
                    <div class="item circle"></div>
                    <!---->
                    <div class="item circle"></div>
                    <div class="item circle"></div>
                    <div class="item circle"></div>
                    <!---->
                    <div class="item sphere"></div>
                    <div class="item sphere"></div>
                </div>
            </div>
        </div>
    </b-modal>
</template>

<script>
    import api, {AUTH_SUCCESS, LOAD_USER} from "../api"
    import {vueTelegramLogin} from 'vue-telegram-login'

    export default {
        props: ['show'],
        name: "LoginModal",
        components: {vueTelegramLogin},
        computed:{
            loginModal: {
                get () {
                    return this.$store.getters.loginModal;
                },
                set (newValue) {
                    return this.$store.dispatch('setLoginModal', newValue);
                }
            },
        },
        data(){
            return {
                loading: false,

                login: '',
                password: '',
                remember: true,

                otp:{
                    enabled: false,
                    code: ''
                }
            }
        },
        methods:{
            vkLogin(){
                api.get('auth/vk/redirect?page=login').then(response => {
                    if (response.data.success){
                        window.location = response.data.url;
                    }
                });
            },
            runCaptcha(){
                this.loading = true;

                if (this.login.length < 3){
                    this.$notify({
                        title: 'Ошибка',
                        text: 'Логин не может быть менее 3 символов!',
                        type: 'warn'
                    });
                    this.loading = false;
                    return;
                }

                if (this.password.length < 6){
                    this.$notify({
                        title: 'Ошибка',
                        text: 'Пароль не может быть менее 6 символов!',
                        type: 'warn'
                    });
                    this.loading = false;
                    return;
                }

                this.$recaptcha('login').then((token) => {
                    api.post('auth/login', {
                        captcha: token,
                        login: this.login,
                        password: this.password,
                        remember: this.remember ? 1 : 0,
                        otp: this.otp.enabled ? this.otp.code : false
                    }).then(response => {
                        if (response.data.success){
                            if(response.data.otp){
                                this.otp.enabled = true;
                            }else if (response.data.token) {
                                this.$store.dispatch(AUTH_SUCCESS, response.data.token);
                                this.$store.dispatch(LOAD_USER);

                                this.$socket.emit('authenticate', {token: response.data.token});

                                this.loginModal = false;
                            }
                        }else {
                            //response.data.message;
                        }

                        this.loading = false;
                    }).catch(error => {
                        console.log(error);
                    }).finally(() => {
                        this.loading = false;
                    });
                })
            }
        },
        watch:{
            '$route': function () {
                //this.loginModal = false;
            }
        }
    }
</script>
