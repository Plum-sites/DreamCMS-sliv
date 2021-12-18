<template>
    <div :class="'category ' + (this.loading ? 'unload' : '')">
        <div class="h2">
            <div class="row align-items-end text-center">
                <div class="col-12 col-md-6 mb-3 mb-md-0 text-md-left px-0 px-sm-3">
                    <h2>Поиск по форуму</h2>
                    <p class="small">Вы можете найти темы по их названию и сообщения по содержанию. Указав игрока, вы увидите только темы и сообщения созданные им.</p>
                </div>
            </div>
        </div>

        <div class="section create">
            <div class="row align-items-center mb-3">
                <div class="col-12 col-sm-4">
                    Введите текст для поиска
                    <small>Поиск в названиях тем и сообщениях</small>
                </div>
                <div class="col-12 col-sm col-md-5 col-xl-4">
                    <input class="form-control form-control-light mt-1 mt-sm-0" placeholder="Текст..." v-model="text">
                </div>
            </div>
            <div class="row align-items-center mb-3">
                <div class="col-12 col-sm-4">
                    Указать пользователя
                    <small>Поиск тем и сообщений только этого игрока</small>
                </div>
                <div class="col-12 col-sm col-md-5 col-xl-4">
                    <user-selector v-model="user"></user-selector>
                </div>
            </div>
            <div class="row align-items-center mt-2">
                <div class="col-12 col-sm-4"></div>
                <div class="col text-center text-sm-left">
                    <a href="#" class="btn_common primary" @click.prevent="search">Поиск</a>
                </div>
            </div>
        </div>

        <div class="section create mt-2">
            <ul class="cabinet_tabs">
                <li :class="tab === 'discussions' ? 'checked' : ''">
                    <a href="#" @click="tab = 'discussions'">Темы</a>
                </li>
                <li :class="tab === 'posts' ? 'checked' : ''">
                    <a href="#" @click="tab = 'posts'">Сообщения</a>
                </li>
            </ul>
        </div>

        <div class="profile" v-if="tab === 'posts'">
            <div class="activity py-4 p-xl-5">
                <h3>Сообщения на форуме</h3>
                <div class="row mt-3">
                    <div class="col">
                        <ul class="">
                            <li class="post" v-for="post in posts.data">
                                <router-link class="user_pic anchor" :to="{name: 'user', params: { login: post.user.login }}">
                                    <img :src="getHeadUrl(post.user.uuid)">
                                </router-link>
                                <h4><router-link :to="{name: 'discussion', params: {slug: post.discussion.slug}}">{{ post.discussion.title }}</router-link></h4>
                                <p class="action">
                                    <router-link :to="{name: 'user', params: {login: post.user.login}}">{{ post.user.login }}</router-link> написал сообщение
                                </p>
                                <p v-html="post.body"></p>
                                <p class="meta">
                                    <span>{{ moment(post.created_at).format('lll') }}</span>
                                </p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="paging" v-if="posts.total >= posts.per_page">
                <b-pagination v-model="posts_page"
                              :total-rows="posts.total"
                              :per-page="posts.per_page">
                </b-pagination>
            </div>
        </div>

        <div class="profile" v-if="tab === 'discussions'">
            <div class="activity py-4 p-xl-5">
                <h3>Темы на форуме</h3>
                <div class="row mt-3">
                    <div class="col">
                        <ul class="">
                            <li class="post" v-for="discussion in discussions.data">
                                <router-link class="user_pic anchor" :to="{name: 'user', params: { login: discussion.user.login }}">
                                    <img :src="getHeadUrl(discussion.user.uuid)">
                                </router-link>
                                <h4><router-link :to="{name: 'discussion', params: {slug: discussion.slug}}">{{ discussion.title }}</router-link></h4>
                                <p class="action">
                                    <router-link :to="{name: 'user', params: {login: discussion.user.login}}">{{ discussion.user.login }}</router-link> создал тему
                                </p>
                                <p class="meta">
                                    <span>{{ moment(discussion.created_at).format('lll') }}</span>
                                </p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="paging" v-if="discussions.total >= discussions.per_page">
                <b-pagination v-model="discussions_page"
                              :total-rows="discussions.total"
                              :per-page="discussions.per_page">
                </b-pagination>
            </div>
        </div>
    </div>
</template>

<script>
import api from "../api";
import UserSelector from "../components/UserSelector";
import moment from "moment";

export default {
    name: "ForumSearch",
    components: {UserSelector},
    data() {
        return {
            loading: false,
            text: '',
            user: null,

            discussions_page: 1,
            posts_page: 1,

            tab: 'discussions',

            posts: [],
            discussions: [],
        }
    },
    methods: {
        search() {
            this.loading = true;

            api.post('forum/search', {
                text: this.text,
                page: this.tab === 'discussions' ? this.discussions_page : this.posts_page,
                user: this.user ? this.user.id : null
            }).then(response => {
                if (response.data.success) {
                    this.posts = response.data.data.posts;
                    this.discussions = response.data.data.discussions;

                    this.$nextTick(function () {
                        this.initSpoilers();
                    });
                }
            }).finally(() => {
                this.loading = false;
            });
        },
        initSpoilers(){
            $(function(){
                $('.spoiler-text').hide();
                $('.spoiler-toggle').click(function(){
                    $(this).next().toggle();
                });
            });
        }
    },
    watch:{
        user: function (newval){
            console.log(newval);
        },
        posts_page: function (newval){
            this.search();
        },
        discussions_page: function (newval){
            this.search();
        }
    }
}
</script>

<style scoped>

</style>