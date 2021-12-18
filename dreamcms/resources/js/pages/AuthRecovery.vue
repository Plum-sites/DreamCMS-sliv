<template>
    <div :class="'inner ' + (this.loading ? 'unload' : '')">
        <div class="headline">
            <h2>Восстановление пароля</h2>
            <p>Если Вы забыли логин, пароль, либо же их сразу вместе, то Вам поможет наша форма восстановления, Вы можете восстановить аккаунт либо при помощи привязанного аккаунта ВКонтакте, либо же через электронную почту.</p>
        </div>
        <div class="recovery" >
            <div class="row section text-center text-sm-left" v-if="this.$route.params.token">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <i class="big fas fa-at"></i>
                    <h3>Установка нового пароля</h3>
                    <div class="row mt-4">
                        <div class="col-12 col-sm col-md-12 pr-0 pr-md-3">
                            <b-input class="form-control-lg" placeholder="Email" v-model="email" :state="emailState" autocomplete="off"></b-input>
                        </div>
                        <div class="col-12 col-sm col-md-12 pr-0 pr-md-3 mt-2">
                            <b-input class="form-control-lg" placeholder="Новый пароль" type="password" v-model="password" :state="passwordState"></b-input>
                        </div>
                        <div class="col-12 col-sm col-md-12 pr-0 pr-md-3 mt-2">
                            <b-input class="form-control-lg" placeholder="Повторите пароль" type="password" v-model="password2" :state="passwordState2"></b-input>
                        </div>
                        <div class="col-12 col-sm-7 col-md-12 mt-2 mt-sm-0 mt-md-3">
                            <a href="#" class="btn_common" @click.prevent="savePassword">Сменить пароль</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row section text-center text-sm-left" v-else>
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <i class="big fab fa-vk"></i>
                    <h3>Восстановить доступ при помощи соц-сетей</h3>
                    <p>Вы предусмотрительно привязали Ваш аккаунт к соц-сетям?</p>
                    <p class="mb-2">Знайте, Вы — большой молодец, и благодаря этому Вы теперь можете всего в пару кликов восстановить свой аккаунт, выбрав сервис ниже!</p>

                    <vue-telegram-login v-if="hotfix"
                        mode="redirect"
                        telegram-login="dreamcms_bot"
                        :redirect-url="'https://' + window.location.hostname + '/oauth/recovery/telegram'"
                        userpic
                        radius="5"
                        requestAccess="write"
                    />

                    <a :href="getIntegrationURL('vkontakte', 'recovery')" class="btn_common info mt-2" style="font-size: 28px"><i class="fab fa-vk"></i></a>
                    <a :href="getIntegrationURL('discord', 'recovery')" class="btn_common info mt-2" style="font-size: 28px"><i class="fab fa-discord"></i></a>
                    <a :href="getIntegrationURL('yandex', 'recovery')" class="btn_common info mt-2" style="font-size: 28px"><i class="fab fa-yandex"></i></a>
                    <a :href="getIntegrationURL('steam', 'recovery')" class="btn_common info mt-2" style="font-size: 28px"><i class="fab fa-steam"></i></a>
                </div>
                <div class="col-12 col-md-6 col-lg-12 col-xl-6">
                    <i class="big fas fa-at"></i>
                    <h3>Восстановить доступ через электронную почту</h3>
                    <p>Менее быстрый способ восстановления аккаунта, укажите почту привязанную к аккаунту и вы получите письмо с уникаольной ссылкой для восстановления пароля</p>
                    <div class="row mt-4">
                        <div class="col-12 col-sm col-md-12 pr-0 pr-md-3">
                            <b-input class="form-control-lg" placeholder="Почта" v-model="email" :state="emailState"></b-input>
                        </div>
                        <div class="col-12 col-sm-7 col-md-12 mt-2 mt-sm-0 mt-md-3">
                            <a href="#" class="btn_common" @click.prevent="emailRecovery">Выслать инструкцию</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center text-center">
                <div class="col-12 col-md-8 pt-4 pb-3">
                    <p class="other">Случилось нечто непредвиденное и Вы не можете восстановить доступ к Вашему аккаунту? Обратитесь в нашу группу ВКонтакте, где администрация проекта поможет Вам решить все возникшие проблемы.</p>
                </div>
                <div class="col-12">
                    <a href="https://vk.me/vk" class="btn_large primary d-inline-block">
                        <span>Группа ВКонтакте</span>
                        <small>Техническая поддержка 24/7</small>
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import api from '../api';
    import {vueTelegramLogin} from 'vue-telegram-login'

    export default {
        name: "AuthRecovery",
        components: {vueTelegramLogin},
        data(){
            return {
                loading: false,
                hotfix: false,

                email: '',
                password: '',
                password2: '',
            }
        },
        mounted() {
            var _this = this;
            this.$nextTick(function (){
                _this.hotfix = true;
            });
        },
        computed:{
            emailState(){
                if (this.email !== ''){
                    return /.+@.+/.test(this.email);
                }
                return null;
            },
            passwordState(){
                if (this.password !== ''){
                    return !(this.password.length < 8 || this.password.length > 40);
                }
                return null;
            },
            passwordState2(){
                return this.password.length > 0 ? this.password === this.password2 : null;
            }
        },
        methods: {
            savePassword(){
                if (this.passwordState && this.passwordState2){
                    this.loading = true;

                    api.post('auth/password/reset', {
                        token: this.$route.params.token,
                        email: this.email,
                        password: this.password,
                        password_confirmation: this.password2,
                    })
                    .finally(() => {
                        this.loading = false;
                    });
                }
            },
            vkRecovery(){
                api.get('auth/vk/redirect?page=recovery').then(response => {
                    if (response.data.success){
                        window.location = response.data.url;
                    }
                });
            },
            emailRecovery(){
                this.loading = true;

                this.$recaptcha('recovery').then((token) => {
                    api.post('auth/password/email', {
                        email: this.email,
                        captcha: token,
                    })
                        .finally(() => {
                            this.loading = false;
                        });
                });
            }
        }
    }
</script>

<style scoped>

</style>