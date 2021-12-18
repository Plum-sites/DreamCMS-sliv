<template>
    <div :class="loading ? 'unload' : ''">
        <div class="section economy">
            <h3>Прокачай свой аккаунт!</h3>
            <p>Расширьте возможности своего аккаунта с приобретением эксклюзивных донат-привилегий. Будьте внимательны! Все группы продаются на один сервер, после покупки его сменить уже будет нельзя. Длительность группы указана на кнопке после выбора группы и сервера!</p>
            <p>
                <router-link class="btn_common primary" :to="{name: 'page', params: {name: 'donate'}}">Описание привилегий</router-link>
                <router-link class="btn_common mt-2 mt-sm-0" :to="{name: 'page', params: {name: 'rules'}}">Наши правила</router-link>
            </p>
            <div class="purchases" v-if="active_groups.length > 0">
                <p>Ваши активные привилегии:</p>
                <div class="row">
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2" v-for="usergroup in active_groups">
                        <div :class="'group ' + usergroup.group.css">
                            <h5>{{ usergroup.group.name }}</h5>
                            <small>{{ usergroup.server.name }}</small>
                            <small class="mt-3">Куплено:</small>
                            <b>{{ formatUnix(usergroup.time) }}</b>
                            <small class="mt-3">Заканчивается:</small>
                            <b>{{ formatUnix(usergroup.expire) }}</b>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="section light text-center px-0 py-4 mt-4">
            <h3>Выберите группу</h3>
        </div>
        <div :class="'row privilege_list justify-content-center ' + (selectedGroup.length > 0 ? 'darken' : '')" v-if="!loading">
            <div class="col-12 col-sm-6 col-lg-4 col-xl">
                <a href="#" :class="'privilege vip ' + (selectedGroup === 'VIP' ? 'checked' : '')" @click.prevent="selectedGroup = 'VIP'">
                    <h5>VIP</h5>
                    <p>{{ getDonateGroup('VIP').price }} МОНЕТ</p>
                    <ul>
                        <li>
                            <b>+4</b> региона по<br>
                            <b>150 тысяч</b> блоков и<br>
                            <b>+6</b> полезных флагов
                        </li>
                        <li>
                            Набор ресурсов<br>
                            уровня <b>VIP</b>
                        </li>
                        <li><b>+9</b> игровых команд</li>
                        <li class="disabled">Полная настройка префикса и суффикса</li>
                        <li class="disabled">Специальные точки телепортации <b>WARP</b></li>
                    </ul>
                </a>
            </div>
            <div class="col-12 col-sm-6 col-lg-4 col-xl mt-3 mt-sm-0">
                <a href="#" :class="'privilege premium ' + (selectedGroup === 'Premium' ? 'checked' : '')" @click.prevent="selectedGroup = 'Premium'">
                    <h5>PREMIUM</h5>
                    <p>{{ getDonateGroup('Premium').price }} МОНЕТ</p>
                    <ul>
                        <li>
                            <b>+6</b> регионов по<br>
                            <b>150 тысяч</b> блоков и<br>
                            <b>+13</b> полезных флагов
                        </li>
                        <li>
                            Набор ресурсов<br>
                            уровня <b>PREMIUM</b>
                        </li>
                        <li><b>+16</b> игровых команд</li>
                        <li>Скин и плащ в <b>HD</b></li>
                        <li>Настройка суффикса</li>
                        <li><b>Все возможности привилегии VIP</b></li>
                    </ul>
                </a>
            </div>
            <div class="col-12 col-sm-6 col-lg-4 col-xl mt-3 mt-lg-0">
                <a href="#" :class="'privilege deluxe ' + (selectedGroup === 'Deluxe' ? 'checked' : '')" @click.prevent="selectedGroup = 'Deluxe'">
                    <h5>DELUXE</h5>
                    <p>{{ getDonateGroup('Deluxe').price }} МОНЕТ</p>
                    <ul>
                        <li>
                            <b>+8</b> регионов по<br>
                            <b>200 тысяч</b> блоков и<br>
                            <b>+18</b> полезных флагов
                        </li>
                        <li>
                            Набор ресурсов<br>
                            уровня <b>DELUXE</b>
                        </li>
                        <li><b>+22</b> игровых команд</li>
                        <li>Скин и плащ в <b>HD</b></li>
                        <li>Полная настройка префикса и суффикса</li>
                        <li><b>Все возможности привилегии PREMIUM</b></li>
                    </ul>
                </a>
            </div>
            <div class="col-12 col-sm-6 col-lg-6 col-xl-4 mt-3 mt-xl-0">
                <a href="#" :class="'privilege legend ' + (selectedGroup === 'Legend' ? 'checked' : '')" @click.prevent="selectedGroup = 'Legend'">
                    <h5>LEGEND</h5>
                    <p>{{ getDonateGroup('Legend').price }} МОНЕТ</p>
                    <ul>
                        <li>
                            <b>+10</b> регионов по<br>
                            <b>600 тысяч</b> блоков<br>
                            <b>Доступны все флаги</b>
                        </li>
                        <li>
                            Набор ресурсов<br>
                            уровня <b>LEGEND</b>
                        </li>
                        <li><b>+30</b> игровых команд</li>
                        <li>Скин и плащ в <b>HD</b></li>
                        <li>Полная настройка префикса и суффикса</li>
                        <li><b>Все возможности привилегии DELUXE</b></li>
                    </ul>
                </a>
            </div>
        </div>
        <div class="section light privilege_buy text-center px-0 py-4 mt-4">
            <h3>Выберите сервер</h3>
            <div class="row mt-3 justify-content-center">
                <b-select class="btn_common btn_common_lg select col-12 col-md-8 col-lg-6 col-xl-5" :options="servers" value-field="id" text-field="name" v-model="selectedServer"></b-select>

                <div class="w-100"></div>
                <a href="#" class="btn_large primary col-12 col-md-8 col-lg-6 col-xl-5 mt-5" @click.prevent="buyGroup" v-if="canBuy()">
                    <span>Купить группу {{ selectedGroup }} {{ getServer(selectedServer) ? ('на сервере ' + getServer(selectedServer).name) : ''}} на {{ Math.round(getDonateGroup(selectedGroup).time / 60/60/24) }} дней</span>
                </a>
            </div>
        </div>
    </div>
</template>

<script>
    import {mapGetters} from "vuex";
    import api from "../api";

    export default {
        name: "CabinetDonate",
        computed: {
            ...mapGetters(['servers'])
        },
        data(){
            return {
                loading: true,

                selectedGroup: '',
                selectedServer: 0,

                groups: [],
                active_groups: []
            }
        },
        mounted(){
            this.load();
        },
        methods:{
            load(){
                this.loading = true;

                api.get('/profile/load')
                .then(response => {
                    if (response.data.success){
                        this.groups = response.data.groups;
                        this.active_groups = response.data.active_groups;
                    }
                }).finally(() =>{
                    this.loading = false;
                });
            },
            getDonateGroup(name){
                return this.groups.find(group => group.name === name);
            },
            getServer(id){
                for (const [key, server] of Object.entries(this.servers)){
                    if (server.id === id) return server;
                }
                return null;
            },
            canBuy(){
                if (this.selectedGroup.length > 0 && this.selectedServer !== 0){
                    return this.getServer(this.selectedServer).donate === 1;
                }
                return false;
            },
            buyGroup(){
                this.loading = true;

                api.post('/profile/group/buy', {
                    server: this.selectedServer,
                    group: this.getDonateGroup(this.selectedGroup).id
                })
                .then(response => {
                    this.loading = false;
                    if (response.data.success){
                        this.load();
                    }
                });
            }
        }
    }
</script>
