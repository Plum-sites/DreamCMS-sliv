<template>
    <div :class="this.loading ? 'unload' : ''">
        <div class="section">
            <div class="row">
                <div class="col-12 col-lg-5">
                    <h3>Параметры безопасности</h3>
                    <p>Не пренебрегайте безопасностью Вашего аккаунта, не используйте один и тот же пароль на разных
                        проектах во избежании взлома.</p>

                    <p>Ниже вы можете привязать сторонние сервисы, что позволит входить в аккаунт и восстанавливать к нему доступ в один клик!</p>

                    <div v-for="integration in integrations">
                        <hr>
                        <p>Вы подключили {{ getDriverName(integration.driver) }}, теперь вы можете быстро и безопасно входить на сайт и восстановить пароль, в случае его утери.</p>

                        <a :href="getIntegrationProfileURL(integration)" class="row justify-content-center align-items-center vk_user mx-0 mt-2" target="_blank">
                            <div class="user_pic">
                                <img :src="integration.data.avatar" alt>
                            </div>
                            <h5 class="col-12 col-sm pl-2">
                                {{ integration.data.name }}
                                <small>{{ integration.data.nickname }}</small>
                            </h5>
                        </a>

                        <a href="#" class="btn_common primary mt-2" @click.prevent="unlink(integration)">Отвязать {{ getDriverName(integration.driver) }}</a>
                    </div>

                    <div v-for="driver in drivers" v-if="!hasIntegration(driver)">
                        <hr>

                        <vue-telegram-login v-if="driver === 'telegram'"
                            mode="redirect"
                            telegram-login="dreamcms_bot"
                            :redirect-url="'https://' + window.location.hostname + '/oauth/link/telegram'"
                            userpic
                            radius="5"
                            requestAccess="write"
                        />

                        <a :href="getIntegrationURL(driver, 'link')" class="btn_common primary mt-2" v-else>Подключить {{ getDriverName(driver) }}</a>
                    </div>
                </div>
                <div class="col-12 col-lg">
                    <div class="mb-3">
                        <div class="baseform">
                            <h4>Подтверждение почты</h4>

                            <div class="row">
                                <div class="col-12 col-lg-6 col-xl-5">Текущая почта</div>
                                <div class="col">
                                    {{ currentEmail }}
                                </div>
                            </div>
                            <div class="row" v-if="confirmedAt > 0">
                                <div class="col-12 col-lg-6 col-xl-5">Ваша почта подтверждена!</div>
                            </div>
                            <div v-else>
                                <div class="row">
                                    <div class="col-12 col-lg-6 col-xl-5">Введите почту для подтверждения</div>
                                    <div class="col">
                                        <input type="email" name="email" v-model="confirmEmail">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-lg-6 col-xl-5">
                                    </div>
                                    <div class="col">
                                        <a href="#" class="btn_common primary" @click.prevent="sendConfirmEmail">Отправить подтверждение</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="airside">
                        <div class="baseform">
                            <h4>Изменить пароль</h4>
                            <div class="row">
                                <div class="col-12 col-lg-6 col-xl-5">Последняя смена пароля</div>
                                <div class="col" v-if="lastPasswordChange">
                                    {{ moment.unix(lastPasswordChange).format('lll') }}
                                </div>
                                <div class="col" v-else>
                                    Не менялся с момента регистрации
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-lg-6 col-xl-5">Введите текущий пароль</div>
                                <div class="col">
                                  <b-input type="password" name="pass" placeholder="••••••••••••••••" v-model="changepass.password"></b-input>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-lg-6 col-xl-5">Придумайте новый пароль</div>
                                <div class="col">
                                  <b-input type="password" name="pass" placeholder="••••••••••••••••" v-model="changepass.new_password" autocomplete="off"></b-input>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-lg-6 col-xl-5">Повторите новый пароль</div>
                                <div class="col">
                                  <b-input type="password" name="pass" placeholder="••••••••••••••••" v-model="changepass.new_password2" autocomplete="off" :state="changepass.new_password.length > 0 && changepass.new_password === changepass.new_password2"></b-input>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-lg-6 col-xl-5">
                                </div>
                                <div class="col">
                                    <label class="mb-3">
                                        <input type="checkbox" class="checkbox" v-model="changepass.logout">
                                        Сбросить все сессии
                                    </label>
                                    <br>
                                    <a href="#" class="btn_common primary" @click.prevent="changePassword" v-if="changepass.new_password === changepass.new_password2">Сменить пароль</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="section light">
            <div class="row">
                <div class="col-12 col-lg-5">
                    <div v-if="otp.enabled">
                        <p>У вас подключена двухэтапная авторизация, при входе на сайт или с нового устройства в
                            лаунчере, потребуется код из приложения для входа. Не потеряйте его!</p>
                    </div>
                    <div v-else>
                        <p>Загрузите приложение Яндекс.Ключ или Google Authenticator на свой телефон, отсканируйте
                            QR-код и введите код в соответствующее поле здесь, после чего Вы сможете быстро
                            авторизоваться на сайте при помощи одноразового пароля из приложения.</p>
                        <div class="text-center mt-3">
                            <img :src="qr_url">
                            <p class="m-0">QR-Code</p>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg mt-4 mt-md-5">
                    <div class="baseform">
                        <h4>Двухэтапная авторизация</h4>
                        <div class="row">
                            <div class="col-12 col-lg-6 col-xl-5">Код из приложения</div>
                            <div class="col">
                                <input type="text" placeholder="123456" v-model="otp.code">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-lg-6 col-xl-5">

                            </div>
                            <div class="col">
                                <a href="#" class="btn_common primary" v-if="!otp.enabled" @click.prevent="changeOTP">Активировать
                                    защиту</a>
                                <a href="#" class="btn_common primary" v-else @click.prevent="changeOTP">Снять защиту
                                    (потребуется код или резервный код)</a>
                            </div>
                        </div>
                    </div>
                    <div class="alert_2step">
                        После активации защиты ни в коем случае НЕ УДАЛЯЙТЕ ПРИЛОЖЕНИЕ до отключения этой функции и сохраните резервные коды, которые вы получите после включения, иначе
                        Вы не сможете зайти в аккаунт без кода из приложения!
                    </div>
                </div>
            </div>
        </div>
        <div class="section spreadsheet mt-3">
            <div class="row">
                <div class="col-9">
                    <h3>Успешные входы в аккаунт за последние 30 дней</h3>
                </div>
                <div class="col">
                    <a href="#" class="btn_common info" @click.prevent="resetSessions">Сбросить все сессии</a>
                </div>
            </div>
            <div class="head d-none d-sm-block mt-5">
                <div class="row">
                    <div class="col-4 col-md">IP адрес</div>
                    <div class="col-4 col-md">Браузер</div>
                    <div class="col-4 col-md">Время</div>
                </div>
            </div>
            <div class="body">
                <div class="row" v-for="log in auth_logs">
                    <div class="col-12 col-sm-4 col-md">
                        {{ log.ip_address }}
                    </div>
                    <div class="col-12 col-sm-4 col-md">
                        Браузер: {{ log.ua.getBrowser().name || 'Неизвестно' }} {{ log.ua.getBrowser().version }} <br>
                        ОС: {{ log.ua.getOS().name || 'Неизвестно' }} {{ log.ua.getOS().version }} <br>
                        Устройство: {{ log.ua.getDevice().vendor || 'Неизвестно' }} {{ log.ua.getDevice().model }} <br>
                        <br>
                    </div>
                    <div class="col-12 col-sm-4 col-md">
                        {{ moment(log.login_at).format('lll') }}
                    </div>
                </div>
            </div>
        </div>

        <b-modal modal-class="modal" hide-header hide-footer content-class="custom_modal" size="lg" v-model="show_codes">
            <div id="modal">
                <div class="window aboutKits">
                    <div class="header mb-2">
                        <h2>Резервные коды</h2>
                        <div class="row justify-content-center">
                            <p class="col-12 col-md-9 p-0">Мы сгенерировали для Вас одноразовые секретные коды. В случае потери доступа к генератору кодов, мы сможете восстановить доступ с помощью кодов ниже. НИКОМУ НЕ ПЕРЕДАВАЙТЕ ИХ!</p>
                            <p class="col-12 mt-3">
                                <a href="#" class="btn_common primary" @click.prevent="saveCodes">Сохранить в файл</a>
                            </p>
                        </div>
                    </div>
                    <div class="body">
                        <div class="row justify-content-center">
                            <ul>
                                <li v-for="code in otp.backup_codes">
                                    <h3>{{ code }}</h3>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </b-modal>
    </div>
</template>

<script>
    import api from '../api';
    import { saveAs } from 'file-saver';
    import {mapGetters} from "vuex";
    import {vueTelegramLogin} from 'vue-telegram-login'
    import UAParser from 'ua-parser-js';

    export default {
        name: "CabinetSecurity",
        computed:{
            ...mapGetters(['user', 'servers'])
        },
        components: {vueTelegramLogin},
        mounted() {
            this.loadSettings();
        },
        data() {
            return {
                loading: true,

                vk: null,
                vk_data: null,
                lastPasswordChange: null,

                currentEmail: '',
                confirmEmail: '',

                changepass: {
                    password: '',
                    new_password: '',
                    new_password2: '',
                    logout: true
                },

                qr_url: '',

                otp: {
                    enabled: false,
                    code: '',
                    tempsecret: '',
                    backup_codes: []
                },

                show_codes: false,
                auth_logs: [],
                telegram_id: false,
                tg_token: '',
                show_tg: false,

                confirmedAt: 0,
                selectedServer: null,

                drivers: [],

                integrations: []
            }
        },
        methods: {
            hasIntegration(driver){
                return this.integrations.find(integration => integration.driver === driver);
            },
            unlink(integration){
                api.get('profile/oauth/unlink/' + integration.driver).then(response => {
                    if (response.data.success){
                        this.loadSettings();
                    }
                });
            },
            link(driver){
                api.get('profile/oauth/link/' + driver).then(response => {
                    if (response.data.success){
                        window.location = response.data.url;
                    }
                });
            },
            getIntegrationProfileURL(integration){
                switch (integration.driver){
                    case 'discord':
                        return "#";
                    case 'vkontakte':
                        return 'https://vk.com/id' + integration.ext_id;
                    case 'steam':
                        return integration.data.user_raw.profileurl;
                    case 'mailru':
                    case 'yandex':
                        return '';
                    case 'telegram':
                        return 'https://t.me/' + integration.data.nickname;
                }
            },
            getDriverName(driver){
                switch (driver){
                    case 'discord':
                        return "Discord";
                    case 'vkontakte':
                        return "ВКонтакте";
                    case 'steam':
                        return "Steam";
                    case 'yandex':
                        return "Яндекс";
                    case 'telegram':
                        return "Telegram";
                    case 'mailru':
                        return "Mail.Ru";
                }
            },
            takeBonus(){
                this.loading = true;

                api.post('settings/email/bonus', {
                    server: this.selectedServer,
                }).then(response => {
                    this.loading = false;
                });
            },
            sendConfirmEmail(){
                this.loading = true;

                this.$recaptcha('email_confirm').then((token) => {
                    api.post('settings/email/confirm', {
                        email: this.confirmEmail,
                        captcha: token
                    }).then(response => {
                        this.loading = false;
                    });
                });
            },
            resetSessions(){
                api.post('settings/save', {
                    reset_sessions: true,
                }).then(response => {
                    if (response.data.success){
                        window.location.reload();
                    }
                });
            },
            loadSettings: function () {
                this.loading = true;

                this.confirmEmail = this.user.email;
                this.currentEmail = this.user.email;
                this.confirmedAt = this.user.email_confirmed_at;

                api.get('settings').then(response => {
                    this.vk = response.data.vk;
                    this.vk_data = response.data.vkData;
                    this.lastPasswordChange = response.data.lastPasswordChange;
                    this.otp.enabled = response.data.otp;
                    this.otp.tempsecret = response.data.tempsecret;
                    this.qr_url = response.data.qr_url;
                    this.auth_logs = response.data.auth_logs;
                    this.telegram_id = response.data.telegram_id;

                    this.drivers = response.data.drivers;
                    this.integrations = response.data.integrations.map(integration => {
                        integration.data = JSON.parse(integration.data);
                        return integration;
                    });

                    this.auth_logs.map(log => {
                        log.ua = new UAParser(log.user_agent);
                        return log;
                    })

                    this.loading = false;
                })
                .catch(error => {
                    console.log(error);
                });
            },
            changePassword: function () {
                this.loading = true;

                api.post('settings/save', {
                    pass: this.changepass.password,
                    newpass: this.changepass.new_password,
                    logout: this.changepass.logout,
                }).then(response => {
                    this.loading = false;

                    if (response.data.success && this.changepass.logout){
                        window.location.reload();
                    }

                    this.loadSettings();
                })
                .catch(error => {
                    console.log(error);
                });
            },
            changeOTP() {
                this.loading = true;

                api.post('settings/save', {
                    code: this.otp.code,
                    tempsecret: this.otp.tempsecret,
                }).then(response => {
                    this.otp.code = this.otp.tempsecret = '';
                    this.loading = false;

                    if (response.data.codes){
                        this.otp.backup_codes = response.data.codes;
                        this.show_codes = true;
                    }

                    this.loadSettings();
                })
                .catch(error => {
                    console.log(error);
                });
            },
            saveCodes(){
                var blob = new Blob([this.otp.backup_codes.join("\n")], {type: "text/plain;charset=utf-8"});
                saveAs(blob, "recovery_codes.txt")
            }
        }
    }
</script>

<style scoped>

</style>