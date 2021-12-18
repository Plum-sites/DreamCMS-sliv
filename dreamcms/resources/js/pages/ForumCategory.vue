<template>
    <loader v-if="!this.loaded"></loader>
    <div v-else>
        <div class="category">
            <div class="h2">
                <div class="row align-items-end text-center">
                    <div class="col-12 col-md-6 mb-3 mb-md-0 text-md-left px-0 px-sm-3">
                        <h2>{{ category.name }}</h2>
                        <p class="small mt-1 d-block">{{ category.description }}</p>
                    </div>
                    <div class="col-12 col-md-6 text-md-right px-0 px-sm-3" v-if="category.childs.length <= 0">
                        <b-dropdown text="Опции" v-if="hasPermission('forum_manager.logs.access')">
                            <b-dropdown-item @click="modals.move.open = true">Перенести</b-dropdown-item>
                            <b-dropdown-item @click="archiveDiscussion">Архивировать</b-dropdown-item>
                          <b-dropdown-item @click="checkAll">Выбрать все</b-dropdown-item>
                          <b-dropdown-item @click="deleteDiscussion">Удалить</b-dropdown-item>
                        </b-dropdown>

                        <router-link v-if="this.isLogged" class="btn_common primary" :to="{name: 'create_discussions', params: {category: category.slug}}">Создать тему</router-link>
                    </div>
                </div>
                <div class="paging" v-if="discussions.total >= discussions.per_page">
                    <b-pagination v-model="current_page"
                                  :total-rows="discussions.total"
                                  :per-page="discussions.per_page"
                                  limit="10"
                    >
                    </b-pagination>
                </div>
            </div>
            <div class="content" v-if="category.childs.length > 0">
                <div class="row" v-for="child in category.childs">
                    <div class="col-12 col-sm-9 col-lg-7">
                        <div class="data">
                            <div class="status d-none d-sm-block">
                                <i class="fas fa-comments-alt"></i>
                            </div>
                            <div>
                                <h3><router-link :to="{name: 'category', params: {slug: child.slug}}">{{ child.name }}</router-link></h3>
                                <p>{{ child.description }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col d-none d-sm-flex d-md-none d-lg-flex">
                        <div class="data">
                            <div class="meta">
                                <h3>{{ child.discussions_count }}</h3>
                                <p>{{ declOfNum(child.discussions_count, ['тема', 'темы', 'тем']) }}</p>
                            </div>
                            <div class="meta">
                                <h3>{{ child.posts_count }}</h3>
                                <p>{{ declOfNum(child.posts_count, ['ответ', 'ответа', 'ответов']) }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-3 d-none d-md-block d-lg-none d-xl-block">
                        <div class="data" v-if="child.last_post">
                            <router-link :to="{name: 'user', params: {login: child.last_post.user.login}}" class="user_pic">
                                <img :src="getHeadUrl(child.last_post.user.uuid)" alt>
                            </router-link>
                            <div>
                                <h4><router-link :to="{name: 'discussion', params: {slug: child.last_post.discussion.slug}}">{{ child.last_post.discussion.title }}</router-link></h4>
                                <p>От <router-link :to="{name: 'user', params: {login: child.last_post.user.login}}">{{ child.last_post.user.login }}</router-link>, {{ humanDiff(child.last_post.created_at) }}</p>
                            </div>
                        </div>
                        <div class="data" v-else>
                            <p class="stub">Нет доступных сообщений для отображения в данный момент</p>
                        </div>
                    </div>
                </div>
            </div>
            <ul class="topic">
                <li class="row align-items-center" v-for="discussion in discussions.data">
                    <router-link :to="{name: 'discussion', params: {slug: discussion.slug}}" class="anchor d-none d-md-block">
                        <i :class="'fal ' + (discussion.pinned ? 'fa-thumbtack' : (discussion.no_reply ? 'fa-lock-alt' : (Object.keys(discussion.users).length > 10 ? 'fab fa-hotjar' : (Object.keys(discussion.users).length > 1 ? 'fa-comment-alt-lines' : 'fa-comment-alt'))))"></i>
                    </router-link>
                    <h4 class="col-12 col-sm-5 mr-4">
                        <router-link :to="{name: 'discussion', params: {slug: discussion.slug}}" class="anchor">{{ discussion.title }}</router-link>
                        <small>
                            <router-link :to="{name: 'user', params: {login: discussion.user.login}}">{{ discussion.user.login }}</router-link>
                        </small>
                    </h4>
                    <div class="col responders d-none d-xl-block">
                        <router-link class="user_pic anchor mr-1" v-for="user in truncate(discussion.users, 5)" :to="{name: 'user', params: {login: user.login}}" :key="user.id">
                            <img :src="getHeadUrl(user.uuid)">
                        </router-link>
                        <div class="more" v-if="Object.keys(discussion.users).length > 5">+{{ Object.keys(discussion.users).length - 5 }}</div>
                    </div>
                    <router-link :to="{name: 'discussion', params: {slug: discussion.slug}}" class="time ml-0 ml-sm-auto ml-xl-4">{{ humanDiff(discussion.updated_at) }} назад</router-link>
                    <span class="meta d-none d-sm-block ml-4">
                                            <i class="fas fa-comments-alt"></i>
                                            {{ discussion.posts_count.total }}
                                        </span>

                    <b-checkbox v-if="hasPermission('forum.discussion.move')" class="ml-auto ml-sm-4" v-model="discussion.checked"></b-checkbox>
<!--                    <input v-if="hasPermission('forum.discussion.move')" type="checkbox" class="checkbox ml-auto ml-sm-4" @change="mark(discussion)">-->
                </li>
            </ul>

            <div class="paging" v-if="discussions.total >= discussions.per_page">
                <b-pagination v-model="current_page"
                              :total-rows="discussions.total"
                              :per-page="discussions.per_page"
                              limit="10"
                >
                </b-pagination>
            </div>

            <b-modal v-model="modals.move.open" hide-footer v-if="hasPermission('forum.discussion.move')">
                <v-select :options="Object.values(forumCategories)" v-model="modals.move.level1" label="name"></v-select>
                <v-select :options="Object.values(modals.move.level1.childs)" v-model="modals.move.level2" label="name"></v-select>
                <v-select :options="Object.values(modals.move.level2.childs)" v-model="modals.move.level3" label="name"></v-select>

                <b-button block @click="moveDiscussion" class="mt-2">Переместить</b-button>
            </b-modal>
        </div>
    </div>
</template>

<script>
    import api from '../api';
    import Loader from "../components/Loader";
    import moment from "moment";
    import {mapGetters} from "vuex";

    export default {
        name: "ForumCategory",
        components: {Loader},
        computed:{
            ...mapGetters(['isLogged', 'forumCategories'])
        },
        data(){
            return {
                loaded: false,
                category: null,
                discussions: [],
                current_page: 1,

                modals:{
                    move: {
                        open: false,
                        to: null,

                        level1: {
                            childs: []
                        },
                        level2: {
                            childs: []
                        },
                        level3: {
                            childs: []
                        },
                    }
                },
            }
        },
        mounted(){
            if (this.$route.params.page){
                this.current_page = this.$route.params.page;
            }
            this.loadCategory();
        },
        methods:{
            checkAll(){
                this.discussions.data.forEach(discussion => {
                  discussion.checked = true;
                });
            },
            archiveDiscussion(){
              api.post('forum/discussion/move',{
                  discussion: this.discussions.data.filter(discussion => discussion.checked).map(discussion => discussion.id),
                  category: 41
              });
            },
            moveDiscussion(){
                api.post('forum/discussion/move',{
                    discussion: this.discussions.data.filter(discussion => discussion.checked).map(discussion => discussion.id),
                    category: this.modals.move.to.id
                });
            },
            deleteDiscussion(){
              this.$bvModal.msgBoxConfirm('Вы уверены что хотите УДАЛИТЬ выбранные темы?', {
                title: 'Подтверждение',
                size: 'sm',
                buttonSize: 'sm',
                okVariant: 'danger',
                okTitle: 'Да',
                cancelTitle: 'Нет',
                footerClass: 'p-2',
                hideHeaderClose: false,
                centered: true
              })
              .then(value => {
                if (value){
                  api.post('forum/discussion/delete',{
                    discussion: this.discussions.data.filter(discussion => discussion.checked).map(discussion => discussion.id),
                  });
                }
              })
            },
            truncate(users, num){
                return users.slice(0, num);
            },
            loadCategory(){
                this.loaded = false;

                api.get('/forum/category/' + this.$route.params.slug + '?page=' + this.current_page).then(response => {
                    this.category = response.data.category;
                    this.discussions = response.data.discussions;

                    this.loaded = true;
                }).catch(error => {
                    console.log(error);
                });
            }
        },
        watch:{
            '$route': function (to) {
                if (to.params.page){
                    this.current_page = to.params.page;
                }
                this.loadCategory();
            },
            'current_page': function (newVal) {
                if (this.category){
                    this.$router.push({name: 'category', params:{
                        slug: this.category.slug,
                        page: newVal
                    }});
                }
            },
            'modals.move.level1' : function (newVal) {
                this.modals.move.to = newVal;
            },
            'modals.move.level2' : function (newVal) {
                this.modals.move.to = newVal;
            },
            'modals.move.level3' : function (newVal) {
                this.modals.move.to = newVal;
            }
        }
    }
</script>