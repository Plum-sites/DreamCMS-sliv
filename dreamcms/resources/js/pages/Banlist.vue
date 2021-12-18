<template>
    <div :class="'inner ' + (loading ? 'unload' : '')">
        <div class="headline">
            <h2>Список блокировок</h2>
            <p>Здесь представлен перечень всех заблокированных пользователей на нашем проекте, так что не нарушайте правила и никогда сюда не попадёте!;)</p>
        </div>

        <div class="banlist mt-5">
            <div class="section primary search">
                <div class="row banned" v-if="bans.length > 0">
                    <i class="fal fa-user-lock d-none d-sm-block"></i>
                    <div class="col pl-0 text-center text-sm-left" >
                        <h4>Вы были заблокированы на наших серверах</h4>
                        <small>Не согласны с выданным наказанием? Подайте <router-link :to="{name: 'category', params: {slug: '19'}}">обжалование блокировки</router-link> на нашем форуме, либо купите досрочный, но платный разбан.</small>
                    </div>
                </div>
                <div class="row align-items-center">
                    <i class="fal fa-search d-none d-sm-block"></i>
                    <input class="col text-center text-sm-left pl-0" placeholder="Введите текст для поиска (минимум 3 символа)..." minlength="1" maxlength="32" v-model="search.text">
                    <v-select class="btn_common select mt-2 mt-md-0 pr-5" :filterable="false" :options="search_subjects" v-model="search.subject" label="text" :reduce="subject => subject.value"></v-select>
                </div>
            </div>
            <div class="section spreadsheet mt-3">
                <div class="head d-none d-sm-block">
                    <div class="row">
                        <div class="col-4 col-md">Игрок</div>
                        <div class="col-4 col-md">Забанил</div>
                        <div class="col-4 col-md">Причина</div>
                        <div class="col-6 col-md">Дата</div>
                        <div class="col-6 col-md">Окончание</div>
                    </div>
                </div>
                <div class="body">
                    <div class="row" v-for="ban in banlist">
                        <div class="col-12 col-sm-4 col-md">
                            <router-link :to="{name: 'user', params: {login: ban.login}}">
                                <div class="user_pic">
                                    <img :src="getHeadUrl(ban.uuid)" alt>
                                </div>
                                {{ ban.login }}
                            </router-link>
                        </div>
                        <div class="col-12 col-sm-4 col-md">
                            <router-link :to="{name: 'user', params: {login: ban.admin}}">
                                <div class="user_pic">
                                    <img :src="getHeadUrl(ban.adminUUID)" alt>
                                </div>
                                {{ ban.admin }}
                            </router-link>
                        </div>
                        <div class="col-12 col-sm-4 col-md">
                            <a href="#" @click.prevent="">{{ ban.Reason }}</a>
                        </div>
                        <div class="col-12 col-sm-6 col-md">{{ moment.unix(ban.Time).format('lll') }}</div>
                        <div class="col-12 col-sm-6 col-md">
                            <span style="color:red" v-if="!ban.Temptime">Никогда</span>
                            <span v-else>{{ moment.unix(ban.Temptime).format('lll') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="paging">
                <b-pagination v-model="pagination.current_page"
                              :total-rows="pagination.total"
                              :per-page="pagination.per_page">
                </b-pagination>
            </div>
        </div>
    </div>
</template>

<script>
    import api from "../api";
    import {mapGetters} from "vuex";

    export default {
        name: "Banlist",
        computed: {
            ...mapGetters(['isLogged'])
        },
        data(){
            return {
                loading: true,
                loadingMyBans: true,

                search_subjects: [
                    {text: 'По логину игрока', value: 'user'},
                    {text: 'По логину модератора', value: 'moder'},
                    {text: 'По причине блокировки', value: 'reason'}
                ],
                search: {
                    text: '',
                    subject: 'user',
                },
                pagination: {
                    current_page: 1,
                    total: 0,
                    per_page: 10
                },
                banlist: [],
                bans: []
            }
        },
        mounted(){
            if (this.isLogged){
                this.loadMyBans();
            }
            this.loadBans();
        },
        methods: {
            loadMyBans(){
                this.loadingMyBans = true;
                api.get('/profile/punish')
                    .then(response => {
                        if (response.data.success){
                            this.bans = response.data.bans;
                        }
                    }).finally(() =>{
                    this.loadingMyBans = false;
                });
            },
            loadBans(){
                this.loading = true;

                api.get('ban/list', {params: {page: this.pagination.current_page, search: this.search.text, subject: this.search.subject}}).then(response => {
                    this.banlist = response.data.bans.data;

                    this.pagination.current_page = response.data.bans.current_page;
                    this.pagination.last_page = response.data.bans.last_page;
                    this.pagination.per_page = response.data.bans.per_page;
                    this.pagination.total = response.data.bans.total;

                    this.loading = false;
                }).catch(error => {
                    console.error(error);
                });
            }
        },
        watch: {
            'search.text' : function (newVal) {
                if (newVal.length >= 3){
                    this.loadBans();
                }else if (newVal.length === 0){
                    this.loadBans();
                }
            },
            'pagination.current_page' : function () {
                this.loadBans();
            },
            'search.subject' : function () {
                if (this.search.text.length >= 3) this.loadBans();
            }
        }
    }
</script>

<style scoped>

</style>