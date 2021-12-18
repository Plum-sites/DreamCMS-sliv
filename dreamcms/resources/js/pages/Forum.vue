<template>
    <div>
        <forum-chat></forum-chat>
        <forum-board></forum-board>

        <loader v-if="forumCategories.length < 1"></loader>

        <div class="category" v-for="mainCategory in forumCategories">
            <div class="h2 text-center text-md-left">
                <h2>{{ mainCategory.name }}</h2>
            </div>
            <div class="content">
                <div class="row" v-for="category in mainCategory.childs">
                    <div class="col-12 col-sm-9 col-lg-6">
                        <div class="data">
                            <div class="status d-none d-sm-block">
                                <i class="fas fa-comments-alt"></i>
                            </div>
                            <div>
                                <h3><router-link :to="{name: 'category', params: {slug: category.slug}}">{{ category.name }}</router-link></h3>
                                <p>{{ category.description }}</p>
                                <ul class="sublist" v-if="category.childs && Object.keys(category.childs).length">
                                    <li v-for="subcategory in category.childs">
                                        <router-link :to="{name: 'category', params: {slug: subcategory.slug}}">{{ subcategory.name }}</router-link>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="col d-none d-sm-flex d-md-none d-lg-flex">
                        <div class="data">
                            <div class="meta">
                                <h3>{{ readableNum(category.discussions_count) }}</h3>
                                <p>{{ declOfNum(category.discussions_count, ['тема', 'темы', 'тем']) }}</p>
                            </div>
                            <div class="meta">
                                <h3>{{ readableNum(category.posts_count) }}</h3>
                                <p>{{ declOfNum(category.posts_count, ['ответ', 'ответа', 'ответов']) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-3 d-none d-md-block d-lg-none d-xl-block">
                        <div class="data" v-if="category.post_preview">
                            <router-link class="user_pic" :to="{name: 'user', params: {login: category.post_preview.user.login}}">
                                <img :src="getHeadUrl(category.post_preview.user.uuid)" alt>
                            </router-link>
                            <div>
                                <h4><router-link :to="{name: 'discussion', params: {slug: category.post_preview.discussion.slug}}">{{ category.post_preview.discussion.title }}</router-link></h4>
                                <p>От <router-link :to="{name: 'user', params: {login: category.post_preview.user.login}}">{{ category.post_preview.user.login }}</router-link>, {{ humanDiff(category.post_preview.created_at) }} назад</p>
                            </div>
                        </div>
                        <p class="stub" v-else>Нет доступных сообщений для отображения в данный момент</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import ForumChat from "../components/ForumChat";
    import ForumBoard from "../components/ForumBoard";
    import {mapGetters} from "vuex";
    import {FORUM_LOAD, FORUM_LOAD_LATEST} from "../api";
    import Loader from "../components/Loader";

    export default {
        name: "Forum",
        components: {Loader, ForumBoard, ForumChat},
        computed:{
            ...mapGetters(['forumCategories'])
        },
        mounted(){
            this.$store.dispatch(FORUM_LOAD);

            this.sockets.subscribe('forum.posts.new', (msgs) => {
                this.$store.dispatch(FORUM_LOAD_LATEST);
            });
        },
        destroyed(){
            this.sockets.unsubscribe('forum.posts.new');
        },
    }
</script>