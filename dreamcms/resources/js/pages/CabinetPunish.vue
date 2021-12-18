<template>
    <div :class="loading ? 'unload' : ''">
        <div class="section economy">
            <h3>История наказаний</h3>
            <p>Нарушая правила, вы получаете блокировку на всех наших серверах. Если вы поняли свою ошибку и хотите исправиться, вы можете приобрести досрочную моментальную разблокировку. С каждой блокировкой, стоимость разбана увеличивается на 50 стримов!</p>
            <p>
                <router-link class="btn_common mt-2 mt-sm-0" :to="{name: 'page', params: {name: 'rules'}}">Наши правила</router-link>
                <router-link class="btn_common primary" :to="{name: 'banlist'}">Бан-лист</router-link>
            </p>
            <div class="purchases" v-if="bans.length > 0">
                <p>Ваши активные наказания:</p>
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2" v-for="ban in bans">
                        <div class="group vip">
                            <h5>Причина: {{ ban.Reason }}</h5>
                            <small v-if="ban.admin">Выдал: {{ ban.admin.login }}</small>
                            <small v-else>Выдан сервером</small>
                            <small class="mt-3">Начало:</small>
                            <b>{{ formatUnix(ban.Time) }}</b>
                            <small class="mt-3">Заканчивается:</small>
                            <b v-if="ban.Temptime">{{ formatUnix(ban.Temptime) }}</b>
                            <b v-else style="color: red">Навсегда</b>
                            <a v-if="ban.price" href="#" class="btn_common primary h6 mt-3" @click.prevent="buy(ban.id)">Купить разбан за {{ ban.price }} стримов</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="purchases" v-else>
                <p>У вас нет активных наказаний, так держать!</p>
            </div>
        </div>
        <div class="banlist" v-if="punish_logs.length > 0">
            <div class="section spreadsheet mt-3">
                <div class="head d-none d-sm-block">
                    <div class="row">
                        <div class="col-4 col-md">Выдал</div>
                        <div class="col-4 col-md">Причина</div>
                        <div class="col-4 col-md">Тип</div>
                        <div class="col-6 col-md">Дата</div>
                        <div class="col-6 col-md">Окончание</div>
                    </div>
                </div>
                <div>
                    <div class="row" v-for="log in punish_logs">
                        <div class="col-12 col-sm-4 col-md">
                            <router-link :to="{name: 'user', params: {login: log.admin.login}}">
                                <div class="user_pic">
                                    <img :src="getHeadUrl(log.UUIDAdmin)" alt>
                                </div>
                                {{ log.admin.login }}
                            </router-link>
                        </div>
                        <div class="col-12 col-sm-4 col-md">
                            <a href="#" @click.prevent="">{{ log.Reason }}</a>
                        </div>
                        <div class="col-12 col-sm-4 col-md">
                            <a href="#" @click.prevent="">{{ getType(log.Type) }}</a>
                        </div>
                        <div class="col-12 col-sm-6 col-md">{{ moment.unix(log.Time).format('lll') }}</div>
                        <div class="col-12 col-sm-6 col-md">
                            <span style="color:red" v-if="!log.TempTime">Никогда</span>
                            <span v-else>{{ formatUnix(log.TempTime) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {mapGetters} from "vuex";
    import api from "../api";

    export default {
        name: "CabinetPunish",
        computed: {
            ...mapGetters(['servers'])
        },
        data(){
            return {
                loading: true,

                bans: [],
                punish_logs: []
            }
        },
        mounted(){
            this.load();
        },
        methods:{
            load(){
                this.loading = true;

                api.get('/profile/punish')
                .then(response => {
                    if (response.data.success){
                        this.bans = response.data.bans;
                        this.punish_logs = response.data.punish_logs;
                    }
                }).finally(() =>{
                    this.loading = false;
                });
            },
            getType(type){
                if (type === 'ban'){
                    return "Бан";
                }
                if (type === 'permban'){
                    return "Вечный бан";
                }
                if (type === 'mute'){
                    return "Блокировка чата";
                }
                if (type === 'kick'){
                    return "Кик с сервера";
                }

                return type;
            },
            getServer(id){
                for (const [key, server] of Object.entries(this.servers)){
                    if (server.id === id) return server;
                }
                return null;
            },
            buy(id){
                this.loading = true;

                api.post('/profile/punish/unban', {
                    ban: id,
                }).finally(() =>{
                    this.load();
                });
            }
        }
    }
</script>
