<template>
    <loader v-if="this.loading"></loader>
    <div class="forum" v-else>
        <div class="row breadcrumbs">
            <div class="col">
                <ul>
                    <li><router-link :to="{name: 'forum'}" class="btn_common primary">Форум</router-link></li>
                    <li v-if="category"><router-link :to="{name: 'category', params: {slug: category.slug}}" class="btn_common info">{{ category.name }}</router-link></li>
                </ul>
            </div>
            <div class="col-7 text-right">
                <a href="#" class="btn_common mr-2 d-none d-md-inline-block">Новые публикации</a>
                <router-link :to="{name: 'forum_search'}" class="btn_common dark">Поиск... <i class="fas fa-search fa-sm ml-2"></i></router-link>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-lg">
                <div class="thread">
                    <div class="row alert align-items-center justify-content-center justify-content-md-start text-center text-md-left mb-3" v-if="discussion.pinned">
                        <i class="fal fa-thumbtack"></i>
                        <div class="col-12 col-md">
                            <h3>Тема закреплена</h3>
                            <p>Обсуждение закреплено. Скорее всего, здесь есть важная информация.</p>
                        </div>
                    </div>

                    <div class="row header align-items-center text-center text-md-left">
                        <div class="user_pic d-none d-md-block">
                            <img :src="getHeadUrl(discussion.user.uuid)" alt>
                        </div>
                        <div class="col">
                            <h3>{{ discussion.title }}</h3>
                            <p>Опубликовано <router-link :to="{name: 'user', params: {login : discussion.user.login}}">{{ discussion.user.login }}</router-link> {{ humanDiff(discussion.created_at) }} назад в раздел <router-link :to="{name: 'category', params: {slug: category.slug}}">{{ category.name }}</router-link></p>
                        </div>
                    </div>

                    <div class="py-3 text-center text-md-right">
                        <b-dropdown text="Опции" v-if="hasPermission('forum_manager.logs.access')">
                            <b-dropdown-item @click="openDiscussion" v-if="discussion.no_reply">Открыть</b-dropdown-item>
                            <b-dropdown-item @click="closeDiscussion" v-else>Закрыть</b-dropdown-item>

                            <b-dropdown-item @click="modals.move.open = true">Перенести</b-dropdown-item>

                            <b-dropdown-item @click="unpinDiscussion" v-if="discussion.pinned">Открепить</b-dropdown-item>
                            <b-dropdown-item @click="pinDiscussion" v-else>Закрепить</b-dropdown-item>

                            <b-dropdown-item @click="archiveDiscussion">Архивировать</b-dropdown-item>
                            <b-dropdown-item @click="deleteDiscussion">Удалить</b-dropdown-item>
                        </b-dropdown>
                        <a href="#" class="btn_common primary ml-2" onclick="$('html, body').animate({ scrollTop: $('#anchor').offset().top}, 1000);">Ответить</a>
                    </div>
                    <div class="body">
                        <div :ref="'#' + post.id" class="row post" v-for="post in posts.data" :id="'post' + post.id">
                            <div class="col-12 col-md author">
                                <div class="row">
                                    <div class="col col-md-12 order-2 order-md-1 text-left text-md-center">
                                        <h5>
                                            <router-link :to="{name: 'user', params: {login: post.user.login}}">{{ post.user.login }}</router-link>
                                        </h5>
                                        <p v-html="post.user.siterole">Игрок</p>
                                    </div>
                                    <div class="col avatar order-1 order-md-2 mt-md-2 mb-md-1">
                                        <router-link class="user_pic anchor" :to="{name: 'user', params: {login: post.user.login}}">
                                            <img :src="getHeadUrl(post.user.uuid)" alt>
                                        </router-link>
                                    </div>
                                    <div class="col order-3 ml-auto ml-md-0 col-md-12 text-right text-md-center">
                                        <!--<p>Не в сети</p>-->
                                        <p>{{ post.user.posts }} {{ declOfNum(post.user.posts, ['сообщение', 'сообщения', 'сообщений']) }}</p>
                                    </div>
                                    <div class="col-12 order-4 d-none d-md-block">
                                        <p :class="'rep ' + (post.user.reputation < -100 ? 'nightmare' : (post.user.reputation < -5 ? 'danger' : (post.user.reputation > 100 ? 'liked' : (post.user.reputation > 5 ? 'primary' : ''))))">
                                            <b>{{ post.user.reputation }}</b>
                                            {{ (post.user.reputation < -100 ? 'Очень плохой' : (post.user.reputation < -5 ? 'Плохой' : (post.user.reputation > 100 ? 'Очень хороший' : (post.user.reputation > 5 ? 'Хороший' : 'Обычный')))) }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-md">
                                <div class="meta">
                                    <span>Опубликовано {{ moment(post.created_at).format('lll') }}</span>
                                    <span class="d-block float-md-right">
                                        <a :href="'#' + post.id">#{{ post.id }}</a>
                                    </span>
                                </div>

                                <div class="content" v-if="editingPost === post.id">
                                    <editor :id="'edit' + post.id" :init="editor.init" :initial-value="post.body" inline></editor>
                                </div>

                                <div class="content" v-html="post.body" v-else></div>

                                <hr>
                                <div v-if="post.user.sign && (isLogged && user.show_signs)" class="sign" v-html="post.user.sign"></div>
                                <hr v-if="post.user.sign && (isLogged && user.show_signs)">
                                <div class="row action align-items-center text-center text-lg-left mr-0">
                                    <div class="col-12 col-lg">
                                        <a href="#" v-if="isLogged && (post.user.id === user.id || hasPermission('forum.post.edit')) && editingPost !== post.id" @click.prevent="editingPost = post.id">Редактировать</a>
                                        <a href="#" v-if="isLogged && (post.user.id === user.id || hasPermission('forum.post.edit')) && editingPost === post.id" @click.prevent="editPost(post)">Сохранить</a>
                                        <a href="#" v-if="isLogged && (post.user.id === user.id || hasPermission('forum.post.delete'))" @click.prevent="deletePost(post.id)">Удалить</a>
                                        <a href="#" v-if="hasPermission('forum_manager.ban.access')" @click.prevent="blockUser(post.user)">Заблокировать</a>
                                        <a href="#" v-if="hasPermission('forum_manager.sign.delete')" @click.prevent="clearSign(post.user)">Очистить подпись</a>
                                    </div>
                                    <div class="mt-2 mt-lg-0 ml-auto mr-lg-0 mr-auto">
                                        <a href="#" v-if="isLogged" class="btn_common danger outline" @click.prevent="likePost(post, -1)">Не нравится</a>
                                        <a href="#" v-if="isLogged" class="btn_common primary outline" @click.prevent="likePost(post, 1)">Нравится</a>
                                        <a href="#" :class="'btn_common ' + (post.likes < 0 ? 'danger' : 'primary')" rel="modal" @click.prevent="openModalLikes(post)" v-if="post.likes !== 0">{{ post.likes }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="anchor"></div>
                    </div>

                    <div class="paging" v-if="pagination.total > 10">
                        <b-pagination v-model="pagination.current_page"
                                      :total-rows="pagination.total"
                                      :per-page="pagination.per_page">
                        </b-pagination>
                    </div>

                    <div class="row alert align-items-center justify-content-center justify-content-md-start text-center text-md-left mt-3" v-if="discussion.no_reply">
                        <i class="fal fa-lock-alt"></i>
                        <div class="col-12 col-md">
                            <h3>Тема закрыта</h3>
                            <p>Вы не можете создавать новые сообщения в этой теме</p>
                        </div>
                    </div>

                    <div class="row footer mt-3" v-else-if="isLogged">
                        <div class="user_pic d-none d-md-block">
                            <img :src="getHeadUrl(user.uuid)" alt>
                        </div>
                        <div class="col">
                            <editor id="newpost" :init="editor.init" v-model="newpost.content"></editor>
                        </div>
                        <div class="col-12 text-right mt-3">
                            <a v-if="hasPermission('forum.template.access')" href="#" class="btn_common btn_common_lg" @click.prevent="modals.template.open = true">Шаблоны</a>
                            <a href="#" class="btn_common btn_common_lg primary" @click.prevent="post">Отправить</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <b-modal v-model="modals.template.open" hide-footer>
            <v-select :options="editor.init.templates" v-model="modals.template.id" :reduce="template => template.id" label="title"></v-select>
            <b-button block @click="deleteTemplate" class="mt-2">Удалить шаблон</b-button>
            <hr>
            <b-input v-model="modals.template.name" placeholder="Имя для шаблона"></b-input>
            <b-button block @click="saveTemplate" class="mt-2">Сохранить шаблон</b-button>
        </b-modal>

        <b-modal v-model="modals.ban.open" hide-footer>
            <h3>Блокировка игрока: {{ modals.ban.user.login }}</h3>
            <b-input v-model="modals.ban.reason" placeholder="Причина" class="mt-2"></b-input>
            <b-input v-model="modals.ban.days" placeholder="Количество дней" max="365" min="1" type="number" class="mt-2"></b-input>
            <b-checkbox v-model="modals.ban.game" class="mt-2">Выдать блокировку в игре</b-checkbox>
            <b-button block @click="performBan" class="mt-2">Выдать блокировку</b-button>
        </b-modal>

        <b-modal v-model="modals.move.open" hide-footer>
            <v-select :options="Object.values(forumCategories)" v-model="modals.move.level1" label="name" @select=""></v-select>
            <v-select :options="Object.values(modals.move.level1.childs)" v-model="modals.move.level2" label="name"></v-select>
            <v-select :options="Object.values(modals.move.level2.childs)" v-model="modals.move.level3" label="name"></v-select>

            <b-button block @click="moveDiscussion" class="mt-2">Переместить</b-button>
        </b-modal>

        <b-modal modal-class="modal" hide-header hide-footer content-class="custom_modal" v-model="modals.likes.open">
            <div id="modal">
                <div class="window listLiked">
                    <div class="header">
                        <div v-if="modals.likes.data.length <= 0">
                            <h2>Оценок нет</h2>
                        </div>
                        <div v-else>
                            <h2>Сообщение оценили:</h2>
                            <div class="row justify-content-center content mt-3 px-3 pb-3">
                                <router-link class="user" v-for="like in modals.likes.data" :to="{name: 'user', params: {login: like.login}}" :key="like.login">
                                    <div class="user_pic">
                                        <img :src="getHeadUrl(like.uuid)" alt>
                                    </div>
                                    <div class="col">
                                        <h3>{{ like.login }}</h3>
                                        <p class="primary" v-if="like.rep >= 1">Положительно</p>
                                        <p class="danger" v-else>Отрицательно</p>
                                    </div>
                                </router-link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </b-modal>
    </div>
</template>

<script>
    import { mapGetters } from "vuex";
    import api, {FORUM_LOAD} from "../api";
    import Loader from "../components/Loader";
    import moment from "moment";

    import tinymce from 'tinymce/tinymce';
    import 'tinymce/themes/silver';

    import Editor from '@tinymce/tinymce-vue';
    import $ from "jquery";

    export default {
        name: "ForumDiscussion",
        components: {
            Loader,
            Editor
        },
        computed: {
            ...mapGetters(['user', 'isLogged', 'forumCategories'])
        },
        mounted(){
            this.$store.dispatch(FORUM_LOAD);

            if (this.$route.params.page){
                this.pagination.current_page = this.$route.params.page;
            }

            this.loadDiscussion();
        },
        data(){
            return {
                modals:{
                    likes: {
                        open: false,
                        loading: true,
                        post: null,
                        data: []
                    },
                    template: {
                        open: false,
                        name: '',
                        id: null
                    },
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
                    },
                    ban: {
                        open: false,
                        user: {
                            login: ''
                        },
                        reason: null,
                        days: null,
                        game: false
                    },
                },

                newpost:{
                    content: ''
                },

                editor:{
                    init:{
                        language: 'ru',
                        icons_url: '/assets/tinymce/icons.min.js',
                        plugins: 'spoiler preview paste autolink autosave code visualblocks visualchars image link media template codesample table hr toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
                        menubar: false,
                        toolbar: 'undo redo | bold italic underline strikethrough | formatselect fontsizeselect | alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist | forecolor removeformat | spoiler-add spoiler-remove | emoticons | preview | image template link anchor codesample',
                        toolbar_sticky: true,
                        templates: [],
                    }
                },

                loading: true,
                category: null,
                discussion: null,
                posts: null,

                editingPost: 0,

                pagination: {
                    current_page: 1,
                    total: 0,
                    per_page: 10
                }
            }
        },
        methods:{
            archiveDiscussion(){
              api.post('forum/discussion/move',{
                discussion: this.discussion.id,
                category: 41
              });
            },
            openModalLikes(post){
                this.modals.likes.loading = true;
                this.modals.likes.open = true;
                api.get('forum/post/like/' + post.id).then(response => {
                    if (response.data.success){
                        this.modals.likes.loading = false;
                        this.modals.likes.data = response.data.users;
                    }
                });
            },
            openDiscussion(){
                api.post('forum/discussion/unlock',{
                    discussion: this.discussion.id,
                }).then(response => {
                    if (response.data.success) this.loadDiscussion();
                });
            },
            closeDiscussion(){
                api.post('forum/discussion/lock',{
                    discussion: this.discussion.id,
                }).then(response => {
                    if (response.data.success) this.loadDiscussion();
                });
            },
            moveDiscussion(){
                api.post('forum/discussion/move',{
                    discussion: this.discussion.id,
                    category: this.modals.move.to.id
                });
            },
            pinDiscussion(){
                api.post('forum/discussion/pin',{
                    discussion: this.discussion.id,
                }).then(response => {
                    if (response.data.success) this.loadDiscussion();
                });
            },
            unpinDiscussion(){
                api.post('forum/discussion/unpin',{
                    discussion: this.discussion.id,
                }).then(response => {
                    if (response.data.success) this.loadDiscussion();
                });
            },
            deleteDiscussion(){
              this.$bvModal.msgBoxConfirm('Вы уверены что хотите УДАЛИТЬ данную тему?', {
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
                        discussion: this.discussion.id,
                      }).then(response => {
                        if (response.data.success){
                          this.$router.push({name: 'category', params: {slug: this.category.slug}})
                        }
                      });
                    }
                  })
            },

            post(){
                api.post('forum/post/create',{
                    chatter_discussion_id: this.discussion.id,
                    body: this.newpost.content,
                })
                .then(response => {
                    if (response.data.success){
                        this.$socket.emit('forum.posts.new');
                        this.newpost.content = '';

                        this.loadDiscussion();
                    }
                });
            },

            blockUser(user){
                this.modals.ban.user = user;
                this.modals.ban.open = true;
            },
            performBan(){
                api.post('forum/user/ban',{
                    user: this.modals.ban.user.id,
                    reason: this.modals.ban.reason,
                    days: this.modals.ban.days,
                    ingame: this.modals.ban.game,
                }).then(response => {
                    if (response.data.success)
                        this.modals.ban.open = false;
                });
            },
            clearSign(user){
                api.post('forum/user/sign/delete',{
                    user: user.id,
                });
            },

            deleteTemplate(){
                api.post('forum/post/template/delete',{
                    id: this.modals.template.id,
                }).then(response => {
                    if (response.data.success){
                        this.modals.template.open = false;
                    }
                });
            },
            saveTemplate(){
                api.post('forum/post/template',{
                    name: this.modals.template.name,
                    body: this.newpost.content,
                }).then(response => {
                    if (response.data.success){
                        this.modals.template.name = '';
                        this.modals.template.open = false;
                    }
                });
            },
            likePost(post, rep){
                api.post('forum/post/like/' + post.id, {
                    like: rep,
                }).then(response => {
                    if (response.data.success) post.likes += rep;
                });
            },
            editPost(post){
                api.post('forum/post/update/' + post.id, {
                    body: tinymce.get('edit' + post.id).getContent(),
                });
            },
            deletePost(id){
                api.post('forum/post/delete/' + id)
                .then(response => {
                    if (response.data.success) this.loadDiscussion();
                });
            },
            initSpoilers(){
                $(function(){
                    $('.spoiler-text').hide();
                    $('.spoiler-toggle').click(function(){
                        $(this).next().toggle();
                    });
                });
            },
            loadDiscussion(){
                this.loading = true;

                api.get('forum/discussion/' + this.$route.params.slug + '?page=' + this.pagination.current_page)
                .then(response => {
                    this.category = response.data.category;
                    this.discussion = response.data.discussion;

                    this.posts = response.data.posts;
                    this.pagination.total = response.data.posts.total;

                    this.editor.init.templates = response.data.templates;

                    this.loading = false;

                    this.$nextTick(function () {
                        this.initSpoilers();
                    });

                    if (this.$route.hash){
                        this.$nextTick(function () {
                            this.$refs[this.$route.hash][0].scrollIntoView();
                        });
                    }
                });
            },
        },
        watch:{
            '$route': function (to) {
                if (to.params.page){
                    this.pagination.current_page = to.params.page;
                }
                this.loadDiscussion();
            },
            'pagination.current_page': function (newVal) {
                if (this.discussion){
                    this.$router.push({name: 'discussion', params:{
                        slug: this.discussion.slug,
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