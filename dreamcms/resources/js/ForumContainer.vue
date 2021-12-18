<template>
    <div id="middle">
        <div class="wrapper">
            <div class="forum">
                <div class="row breadcrumbs">
                    <div class="col">
                        <ul>
                            <li><router-link :to="{name: 'forum'}" class="btn_common primary">Форум</router-link></li>
                        </ul>
                    </div>
                    <div class="col-7 text-right">
<!--                        <a href="#" class="btn_common mr-2 d-none d-md-inline-block">Новые публикации</a>-->
                        <router-link :to="{name: 'forum_search'}" class="btn_common dark">Поиск... <i class="fas fa-search fa-sm ml-2"></i></router-link>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-lg">
                        <router-view></router-view>
                    </div>
                    <div class="col col-lg-4 col-xl-3 mt-5 mt-lg-0">
                        <div class="widget">
                            <h3>Последние ответы</h3>
                            <ul class="widget_list">
                                <li v-for="post in lastPosts">
                                    <router-link class="user_pic" :to="{name: 'user', params: {login: post.user.login}}">
                                        <img :src="getHeadUrl(post.user.uuid)" alt>
                                    </router-link>
                                    <h4>
                                        <router-link :to="{name: 'discussion', params: {slug: post.discussion.slug}}">{{ post.discussion.title }}</router-link>
                                        <small>
                                            <router-link :to="{name: 'user', params: {login: post.user.login}}">{{ post.user.login }}</router-link>, {{ humanDiff(post.created_at) }} назад
                                        </small>
                                    </h4>
                                </li>
                            </ul>
                        </div>
                        <div class="widget">
                            <h3>Лидеры сообщества</h3>
                            <ul class="widget_list">
                                <li v-for="(leader, key) in leaders">
                                    <router-link class="user_pic" :to="{name: 'user', params: {login: leader.login}}">
                                        <img :src="getHeadUrl(leader.uuid)" alt>
                                    </router-link>

                                    <h4>
                                        <router-link :to="{name: 'user', params: {login: leader.login}}">{{ leader.login }}</router-link>
                                        <small>{{ readableNum(leader.posts) }} сообщений</small>
                                    </h4>
                                    <span class="stage">{{ key + 1 }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="widget">
                            <h3>Популярные авторы</h3>
                            <ul class="widget_list">
                                <li v-for="(user, key) in populars">
                                    <router-link class="user_pic" :to="{name: 'user', params: {login: user.login}}">
                                        <img :src="getHeadUrl(user.uuid)" alt>
                                    </router-link>
                                    <h4>
                                        <router-link :to="{name: 'user', params: {login: user.login}}">
                                            {{ user.login }}
                                        </router-link>
                                        <small>+{{ user.reputation }} репутации</small>
                                    </h4>
                                    <span class="stage">{{ key + 1 }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import moment from "moment";
    import api, {FORUM_LOAD, FORUM_LOAD_LATEST, FORUM_LOAD_LEADERS, FORUM_LOAD_POPULARS} from './api';
    import {mapGetters} from "vuex";

    export default {
        name: "ForumContainer",
        data(){
            return {
                interval: false
            }
        },
        created(){
            this.$store.dispatch(FORUM_LOAD);

            this.$store.dispatch(FORUM_LOAD_LATEST);
            this.$store.dispatch(FORUM_LOAD_LEADERS);
            this.$store.dispatch(FORUM_LOAD_POPULARS);

            this.$socket.emit('forum.online');

            this.interval = setInterval(() => {
                this.$socket.emit('forum.online');
            }, 30000);
        },
        beforeDestroy(){
            clearInterval(this.interval);
        },
        computed:{
            ...mapGetters(['isLogged', 'lastPosts', 'leaders', 'populars'])
        }
    }
</script>

<style scoped>

</style>