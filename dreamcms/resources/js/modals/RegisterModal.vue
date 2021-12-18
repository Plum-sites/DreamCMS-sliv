<template>
    <b-modal v-model="registerModal" modal-class="modal" hide-header hide-footer content-class="custom_modal" size="lg">
        <div id="modal">
            <div :class="'window auth ' + (this.loading ? 'unload' : '')">
                <div class="row">
                    <div class="col-12 col-sm-9">
                        <div class="header">
                            <h2>Регистрация</h2>
                            <p>Что-то пошло не так? Сообщите об этом в нашу <a href="https://vk.com/vk" target="_blank">группу ВКонтакте</a> и мы оперативно поможем Вам решить проблему!</p>
                        </div>
                        <div class="body">
                            <div class="row">
                                <div class="col-12 col-md-5">
                                    <label>Придумайте ник</label>
                                    <small>Убедитесь, что он соответствует <router-link :to="{name: 'page', params:{name: 'rules'}}" target="_blank">правилам</router-link></small>
                                </div>
                                <div class="col-12 col-sm-9 col-md mt-1 mt-md-0">
                                    <b-input class="form-control-lg" placeholder="Логин" v-model="login" :state="loginState" autocomplete="off"></b-input>
                                    <small style="color: red;" v-if="!this.loginValid">Данный логин уже занят!</small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-5">
                                    <label>Электронная почта</label>
                                    <small>Указывайте действующий адрес, потребуется его подтвердить</small>
                                </div>
                                <div class="col-12 col-sm-9 col-md mt-1 mt-md-0">
                                    <b-input class="form-control-lg" placeholder="example@mail.ru" v-model="email" :state="emailState" autocomplete="off"></b-input>
                                    <small style="color: red;" v-if="this.email !== '' && !this.emailState">Введите правильный адрес почты!</small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-md-5">
                                    <label>Ваш пароль <a href="#" :class="'fa ' + (showPass ? 'fa-eye-slash' : 'fa-eye')" @click="showPass = !showPass"></a></label>
                                    <small>Не используйте один и тот же пароль на разных проектах!</small>
                                </div>
                                <div class="col-12 col-sm-9 col-md mt-1 mt-md-0">
                                    <b-input class="form-control-lg" :type="showPass ? 'text' : 'password'" placeholder="••••••••••••••••" v-model="password" :state="passwordState" autocomplete="off"></b-input>
                                    <small style="color: red;" v-if="this.password !== '' && !this.passwordState">Пароль должен быть от 6 до 40 символов!</small>
                                </div>
                            </div>
                            <div class="row align-items-center">
                                <div class="col-12 col-md-5">
                                    <label>Повторите пароль</label>
                                </div>
                                <div class="col-12 col-sm-9 col-md mt-1 mt-md-0">
                                    <b-input class="form-control-lg" type="password" placeholder="••••••••••••••••" v-model="check_password" :state="passwordState2" autocomplete="off"></b-input>
                                    <small style="color: red;" v-if="this.password !== '' && !this.passwordState2">Повтор пароля не совпадает!</small>
                                </div>
                            </div>
                            <div class="row mt-2 mt-md-3">
                                <div class="col-12 col-md-5"></div>
                                <label class="col-12 col-sm-9 col-md">
                                    <input type="checkbox" class="checkbox" v-model="rulesAccepted">
                                    <small>Я полностью согласен и ознакомлен с <router-link :to="{name: 'page', params:{name: 'rules'}}" target="_blank">правилами</router-link></small>
                                </label>
                            </div>
                            <div class="row mt-3">
                                <div class="col-12 col-md-5"></div>
                                <div class="col-12 col-sm-9 col-md">
                                    <a href="#" :class="'btn_common primary ' + (allGood ? '' : 'disabled')" @click.prevent="runCaptcha">Зарегистрироваться</a>
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
    import api, {AUTH_SUCCESS, LOAD_USER} from '../api';
    import _ from "lodash";

    export default {
        name: "RegisterModal",
        computed:{
            registerModal: {
                get () {
                    return this.$store.getters.registerModal;
                },
                set (newValue) {
                    return this.$store.dispatch('setRegisterModal', newValue);
                }
            },
            loginState(){
                if (this.login !== ''){
                    if (this.login.length < 3) return false;
                    if (this.login.length > 16) return false;

                    return /^[a-zA-Z0-9_]+$/.test(this.login) && this.loginValid;
                }
                return null;
            },
            emailState(){
                if (this.email !== ''){
                    return /.+@.+/.test(this.email);
                }
                return null;
            },
            passwordState(){
                if (this.password !== ''){
                    return !(this.password.length < 6 || this.password.length > 40);
                }
                return null;
            },
            passwordState2(){
                if (this.password !== '' && this.check_password !== ''){
                    return this.password.length > 0 ? this.password === this.check_password : null;
                }
                return null;
            },
            allGood(){
                return this.rulesAccepted && this.loginState && this.passwordState && this.passwordState2 && this.emailState;
            }
        },
        data(){
            return {
                loading: false,
                loginValid: true,

                rulesAccepted: false,
                login: '',
                password: '',
                check_password: '',
                email: '',

                showPass: false
            }
        },
        methods:{
            checkLogin: _.debounce((vm) => {
                api.post('auth/register/login/check', {
                    login: vm.login,
                }).then(response => {
                    vm.loginValid = response.data.success;
                }).catch(error => {
                    console.log(error);
                });
            }, 1000),
            runCaptcha(){
                if (!this.rulesAccepted){
                    this.$notify({
                        title: 'Ошибка',
                        text: 'Для создания аккаунта на нашем ресурсе, вы должны принять правила!',
                        type: 'warn'
                    });
                    return;
                }

                if (!this.loginState){
                    this.$notify({
                        title: 'Ошибка',
                        text: 'Логин менее 3 или более 16 символов или уже занят!',
                        type: 'warn'
                    });
                    return;
                }

                if (!(this.password !== '' && this.passwordState && this.passwordState2)){
                    this.$notify({
                        title: 'Ошибка',
                        text: 'Пароль менее 6 символов или не совпадает с проверочным',
                        type: 'warn'
                    });
                    return;
                }

                this.loading = true;
                this.$recaptcha('register').then((token) => {
                    api.post('auth/register', {
                        captcha: token,
                        login: this.login,
                        password: this.password,
                        email: this.email,
                        refer: localStorage.getItem('refer')
                    }).then(response => {
                        if (response.data.success){
                            if (response.data.token) {
                                this.$store.dispatch(AUTH_SUCCESS, response.data.token);
                                this.$store.dispatch(LOAD_USER);
                                this.$socket.emit('authenticate', {token: response.data.token});

                                this.registerModal = false;
                            }
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
            login: function (newVal) {
                if (this.login.length > 3){
                    this.checkLogin(this);
                }else {
                    this.loginValid = true;
                }
            }
        }
    }
</script>
