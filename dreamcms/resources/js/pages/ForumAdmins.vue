<template>
    <div :class="'category ' + (loading ? 'unload' : '')">
        <div class="h2 text-center text-md-left">
            <h2>Администрация</h2>
            <p class="small mt-1 d-block">Всегда актуальный список администраторов и модераторов, которые вкладывают свои силы и с каждым днём делают наш проект всё лучше и лучше!</p>
        </div>
        <div class="members">
            <div class="head">
                <div class="row">
                    <div class="col-12 d-block d-sm-none empty">Пользователь, роль и публикации</div>
                    <div class="col col-lg-3 d-none d-sm-block empty">Имя пользователя</div>
                    <div class="col d-none d-sm-block">Роль</div>
                    <div class="col d-none d-md-block d-lg-none d-xl-block">Публикации</div>
                    <div class="col d-none d-sm-block">Активность</div>
                </div>
            </div>
            <div class="body">
                <div class="row align-items-center" v-for="user in users">
                    <div class="col-12 col-sm col-lg-3 order-1 order-sm-0">
                        <router-link class="anchor" :to="{name: 'user', params: { login: user.login }}">
                            <div class="user_pic">
                                <img :src="getHeadUrl(user.uuid)">
                            </div>
                            {{ user.login }}
                        </router-link>
                    </div>
                    <div class="col-6 col-sm order-2 order-sm-0">
                        <div v-html="user.role"></div>
                    </div>
                    <div class="col-12 col-sm d-block d-sm-none d-md-block d-lg-none d-xl-block order-4 order-sm-0">
                        <a href="#">{{ user.posts }} {{ declOfNum(user.posts, ['сообщение', 'сообщения', 'сообщений']) }}</a>
                    </div>
                    <div class="col d-none d-sm-block">
                        <a v-if="user.last_activity">{{ formatUnix(user.last_activity) }}</a>
                        <a v-else>Никогда</a>
                    </div>
<!--                    <div class="col-6 col-sm d-block d-sm-none d-lg-block action order-3 order-sm-0">-->
<!--                        <a href="#" class="btn_common mr-1">-->
<!--                            <i class="fas fa-minus"></i>-->
<!--                        </a>-->
<!--                        <a href="#" class="btn_common primary">-->
<!--                            <i class="fas fa-comment-alt-lines"></i>-->
<!--                        </a>-->
<!--                    </div>-->
                </div>
            </div>
        </div>
        <div class="paging px-0">
            <b-pagination v-model="pagination.current_page"
                          :total-rows="pagination.total"
                          :per-page="pagination.per_page">
            </b-pagination>
        </div>
    </div>
</template>

<script>
    import api from "../api";

    export default {
        name: "ForumAdmins",
        mounted() {
            this.load();
        },
        data(){
            return {
                loading: true,
                users: [],
                pagination: {
                    current_page: 1,
                    total: 0,
                    per_page: 10
                },
            }
        },
        methods: {
            load(){
                this.loading = true;

                api.get('/forum/admins', {
                    params: {
                        page: this.pagination.current_page
                    }
                })
                .then(response => {
                    if (response.data.success){
                        this.users = response.data.users.data;

                        this.pagination.current_page = response.data.users.current_page;
                        this.pagination.last_page = response.data.users.last_page;
                        this.pagination.per_page = response.data.users.per_page;
                        this.pagination.total = response.data.users.total;
                    }
                }).finally(() =>{
                    this.loading = false;
                });
            }
        },
        watch:{
            'pagination.current_page' : function () {
                this.load();
            }
        }
    }
</script>