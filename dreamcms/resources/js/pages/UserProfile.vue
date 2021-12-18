<template>
    <div class="forum">
        <loader v-if="this.loading"></loader>
        <div class="profile" v-else>
            <div class="row align-items-center align-items-lg-start mx-0 px-4">
                <div class="col-12 col-sm order-3 order-sm-1 viewer">
                    <div id="user_viewer"></div>
                </div>
                <div class="col-12 col-sm order-sm-2 header pr-0">
                    <h2>Профиль {{ profile.user.login }}</h2>
                    <p>Аккаунт зарегистрирован: <b>{{ moment.unix(profile.user.reg_time).format('lll') }}</b></p>
                    <p>
                        Действующая группа:
                        <span v-html="profile.role"></span>
                    </p>
                </div>
            </div>
            <div class="row section mx-0 p-4">
                <div class="col-12 col-lg viewer px-0 px-xl-3">
                    <div v-if="!myProfile">
                        <a href="#" class="btn_common d-lg-block" v-if="isMyFriend" @click="removeFromFriend">Убрать из друзей</a>
                        <a href="#" class="btn_common d-lg-block" v-else-if="profile.has_friend_request && !profile.has_sent_friend_request">Вы подписаны</a>
                        <a href="#" class="btn_common d-lg-block" v-else-if="isLogged && !profile.has_sent_friend_request" @click="sendFriendRequest">Добавить в друзья</a>
                        <a href="#" class="btn_common d-lg-block" v-if="profile.has_sent_friend_request" @click="acceptFriend(profile.user.id)">Принять заявку</a>
                    </div>

                    <router-link :to="{name: 'cabinet'}" class="btn_common primary ml-0 ml-sm-2 ml-lg-0 mt-2 mt-sm-0 mt-lg-2 d-lg-block" v-if="myProfile">Личный кабинет</router-link>
                    <a href="#" class="btn_common primary ml-0 ml-sm-2 ml-lg-0 mt-2 mt-sm-0 mt-lg-2 d-lg-block" @click.prevent="" v-else>Личные сообщения</a>
                </div>
                <div class="col-12 col-lg">
                    <ul class="meta">
                        <li>
                            <i class="fas fa-badge-check"></i>
                            <h5>{{ Math.round(moment.duration(moment().diff(moment.unix(profile.user.reg_time))).asDays()) }}</h5>
                            <small>Дней с момента<br>регистрации</small>
                        </li>
                        <li>
                            <i class="fas fa-comments-alt"></i>
                            <h5>{{ profile.posts }}</h5>
                            <small>Сообщений<br>опубликовано</small>
                        </li>
                        <li>
                            <i class="fas fa-heart"></i>
                            <h5>{{ profile.user.reputation }}</h5>
                            <small>Репутации получено<br>от пользователей</small>
                        </li>
                        <li>
                            <i class="fas fa-paper-plane"></i>
                            <h5>{{ profile.avg_chars }}</h5>
                            <small>В среднем символов в<br>одном сообщении</small>
                        </li>
                        <li>
                            <i class="fas fa-waveform-path"></i>
                            <h5>{{ profile.discussions }}</h5>
                            <small>Всего поучавствовал<br>в темах</small>
                        </li>
                    </ul>
                </div>
                <div class="col-12 col-xl-4 px-0">
                    <div class="side">
                        <div class="row">
                            <div class="col-12 col-lg-6 col-xl-12 mt-3">
                                <div class="info">
                                    <ul>
                                        <li>
                                            <b>Был в игре:</b>
                                            <span>{{ profile.user.last_play ? (moment.unix(profile.user.last_play).format('lll')) : 'Никогда' }}</span>
                                        </li>
                                        <li>
                                            <b>Блокировка на форуме:</b>
                                            <span v-if="profile.banned">Заблокирован</span>
                                            <small v-else>Не найдено</small>
                                        </li>
                                        <li>
                                            <b>Блокировка в игре:</b>
                                            <span v-if="profile.game_banned">Заблокирован</span>
                                            <small v-else>Не найдено</small>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-12 col-lg-6 col-xl-12 mt-3">
                                <div class="info">
                                    <h4>
                                        Друзья<span class="count">{{ profile.friends.length }}</span>
                                    </h4>

                                    <div v-if="profile.hide_friends && profile.friends.length === 0">
                                        <small class="d-block mt-1">Этот пользователь скрыл список своих друзей.</small>
                                    </div>
                                    <ul class="row list mt-1" v-else-if="profile.friends.length">
                                        <li v-for="friend in profile.friends.slice(0, 20)">
                                            <router-link class="user_pic anchor" :to="{name: 'user', params: {login: friend.login}}">
                                                <img :src="getHeadUrl(friend.uuid)">
                                            </router-link>
                                        </li>
                                    </ul>
                                    <div v-else>
                                        <small class="d-block mt-1" v-if="myProfile">Вы ещё не никого не добавляли в друзья.</small>
                                        <small class="d-block mt-1" v-else>Этот пользователь ещё никого не добавлял.</small>
                                    </div>
                                    <small class="expand">
                                        <a href="#friends" rel="modal" @click.prevent="openFriendsModal">Открыть полный список друзей</a>
                                    </small>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-xl-12 mt-3">
                                <div class="info">
                                    <h4>
                                        Достижения<span class="count">0 из 56</span>
                                        <small class="d-block float-sm-right">Прогресс 0%</small>
                                    </h4>
                                    <small class="d-block mt-2" v-if="myProfile">Вы ещё не получали никаких достижений.</small>
                                    <small class="d-block mt-1" v-else>Этот пользователь ещё не получал достижений.</small>
                                    <small class="expand mt-1">
                                        <a href="#achievements" rel="modal" @click.prevent="modals.achievements.open = true">Открыть список достижений</a>
                                    </small>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-xl-12 mt-3">
                                <div class="info">
                                    <h4>Последние посетители</h4>
                                    <small class="d-block mt-2" v-if="myProfile">Ваш профиль ещё никто не посетил.</small>
                                    <small class="d-block mt-1" v-else>Список последних посетителей пуст.</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="activity py-4 p-xl-5">
                <h3>Последняя активность</h3>
                <div class="row mt-3">
                    <div class="col">
                        <ul class="">
                            <li class="post" v-for="post in profile.actions">
                                <router-link class="user_pic anchor" :to="{name: 'user', params: {login: profile.user.login}}">
                                    <img :src="getHeadUrl(profile.user.uuid)">
                                </router-link>
                                <h4><router-link :to="{name: 'discussion', params: {slug: post.slug}}">{{ post.title }}</router-link></h4>
                                <p class="action">
                                    <router-link :to="{name: 'user', params: {login: profile.user.login}}">{{ profile.user.login }}</router-link> написал сообщение
                                </p>
                                <p v-html="post.body"></p>
                                <p class="meta">
                                    <span>{{ moment(post.created_at).format('lll') }}</span>
                                </p>
                            </li>
                        </ul>
                        <div class="paging" v-if="profile.posts > 10">
                            <b-pagination v-model="current_page"
                                          :total-rows="profile.posts"
                                          :per-page="10"
                                          limit="10"
                            >
                            </b-pagination>
                        </div>
                    </div>
                    <div class="col-12 col-xl-4 ml-0 ml-xl-4"></div>
                </div>
            </div>

            <b-modal modal-class="modal" hide-header hide-footer content-class="custom_modal" size="lg" v-model="modals.friends.open">
                <div id="modal">
                    <div class="window sectional">
                        <div class="header">
                            <h2 v-if="myProfile">Мой список друзей</h2>
                            <h2 v-else>Список друзей <router-link :to="{name: 'user', params: {login: profile.user.login}}">{{ profile.user.login }}</router-link></h2>
                            <a href="#" class="d-block d-sm-none mt-2" @click.prevent="modals.friends.open = false">Закрыть <i class="far fa-window-close"></i></a>
                        </div>
                        <div class="body">
                            <div class="chapter">
                                <div class="row">
                                    <div class="col-12">
                                        <h3 v-if="profile.friends.length > 0">
                                            Список друзей <b>{{ profile.friends.length }}</b>
                                        </h3>
                                        <h3 v-else>
                                            Список друзей <b>Пуст</b>
                                        </h3>
                                    </div>
                                    <router-link :to="{name: 'user', params: {login: friend.login}}" class="col data" v-for="friend in profile.friends" :key="friend.login">
                                        <div class="user_pic">
                                            <img :src="getHeadUrl(friend.uuid)" alt>
                                        </div>
                                        <h4>
                                            <span>{{ friend.login }}</span>
                                        </h4>
                                    </router-link>
                                </div>
                            </div>

                            <loader v-if="modals.friends.loading"></loader>
                            <div class="chapter" v-else-if="myProfile">
                                <h3>
                                    Входящие заявки
                                    <b>{{ modals.friends.requests.length }}</b>
                                </h3>
                                <ul class="requests">
                                    <li class="row" v-for="request in modals.friends.requests">
                                        <div class="col-12 col-md">
                                            <span>{{ moment(request.created_at).format('lll') }}</span>
                                            <router-link :to="{name: 'user', params: {login: request.sender.login}}">{{ request.sender.login }}</router-link> отправил заявку в друзья
                                        </div>
                                        <a href="#" class="btn_common primary" @click.prevent="acceptFriend(request.sender.id)">Принять</a>
                                        <a href="#" class="btn_common" @click.prevent="denyFriend(request.sender.id)">Отклонить</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </b-modal>

            <b-modal modal-class="modal" hide-header hide-footer content-class="custom_modal" size="lg" v-model="modals.achievements.open">
                <div id="modal">
                    <div class="window sectional">
                        <div class="header">
                            <h2>Достижения <router-link :to="{name: 'user', params: {login: profile.user.login}}">{{ profile.user.login }}</router-link></h2>
                            <p>Достижения находятся в разработке!</p>
                            <p>Пользователь получил 0 из 112 достижений</p>
                        </div>
                        <div class="body">
                            <div class="chapter">
                                <div class="row">
                                    <div class="col-12">
                                        <h3>
                                            Сайт и форум
                                            <b>0 из 17</b>
                                            <small>Общий прогресс 0%</small>
                                        </h3>
                                    </div>
                                    <div class="col-12 col-sm-6 cell">
                                        <i class="fad fa-chess-queen"></i>
                                        <h4>КвинТет!</h4>
                                        <p>Подружиться с экс-куратором форума</p>
                                    </div>
                                    <div class="col-12 col-sm-6 cell">
                                        <i class="fad fa-lock-alt"></i>
                                        <h4>Абсолютная ВЛА-А-А-АСТЬ!</h4>
                                        <p>Войти в состав администрации проекта</p>
                                    </div>
                                    <div class="col-12 col-sm-6 cell">
                                        <i class="fad fa-lock-alt"></i>
                                        <h4>Прокуратор судеб</h4>
                                        <p><em>Особое секретное достижение</em></p>
                                    </div>
                                    <div class="col-12 col-sm-6 cell">
                                        <i class="fad fa-arrows"></i>
                                        <h4>Si vis pacem, para bellum</h4>
                                        <p>Хочешь мира — готовься к войне</p>
                                    </div>
                                    <div class="col-12 col-sm-6 cell">
                                        <i class="fad fa-map-marker-times"></i>
                                        <h4>Статус «Секуляризован»</h4>
                                        <p><em>Открыто секретное достижение!</em></p>
                                    </div>
                                    <div class="col-12 col-sm-6 cell">
                                        <i class="fad fa-lock-alt"></i>
                                        <h4>Статус «Экскомьюникадо»</h4>
                                        <p><em>Особое секретное достижение</em></p>
                                    </div>
                                </div>
                            </div>
                            <div class="chapter">
                                <div class="row">
                                    <div class="col-12">
                                        <h3>
                                            Игровой процесс
                                            <b>0 из 20</b>
                                            <small>Общий прогресс 0%</small>
                                        </h3>
                                    </div>
                                    <div class="col-12 col-sm-6 cell">
                                        <i class="fad fa-lock-alt"></i>
                                        <h4>Школьная фондовая биржа</h4>
                                        <p>Накопить 100 тысяч коинов на любом сервере</p>
                                    </div>
                                    <div class="col-12 col-sm-6 cell">
                                        <i class="fad fa-lock-alt"></i>
                                        <h4>Финансовый воротила</h4>
                                        <p>Перевести на любой сервер 10 тысяч коинов</p>
                                    </div>
                                    <div class="col-12 col-sm-6 cell">
                                        <i class="fad fa-lock-alt"></i>
                                        <h4>бАгАч на сервИре</h4>
                                        <p>Войти в топ-5 игроков по коинам любого сервер</p>
                                    </div>
                                    <div class="col-12 col-sm-6 cell">
                                        <i class="fad fa-lock-alt"></i>
                                        <h4>Юный капиталист</h4>
                                        <p>Занять 1 место в рейтинге по коинам любого сервера</p>
                                    </div>
                                </div>
                            </div>
                            <div class="chapter">
                                <div class="row">
                                    <div class="col-12">
                                        <h3>
                                            Платные услуги
                                            <b>0 из 57</b>
                                            <small>Общий прогресс 0%</small>
                                        </h3>
                                    </div>
                                    <div class="col-12 col-sm-6 cell">
                                        <i class="fad fa-lock-alt"></i>
                                        <h4>Оппа! Чирик!</h4>
                                        <p>Положить ровно 10 рублей на баланс</p>
                                    </div>
                                    <div class="col-12 col-sm-6 cell">
                                        <i class="fad fa-lock-alt"></i>
                                        <h4>Саня, верни сотку!</h4>
                                        <p>Положить ровно 100 рублей на баланс</p>
                                    </div>
                                    <div class="col-12 col-sm-6 cell">
                                        <i class="fad fa-lock-alt"></i>
                                        <h4>Экономил на обедах</h4>
                                        <p>Положить менее 100 рублей на баланс</p>
                                    </div>
                                    <div class="col-12 col-sm-6 cell">
                                        <i class="fad fa-lock-alt"></i>
                                        <h4>Прикуриваю от купюр</h4>
                                        <p>Пополнить баланс на 1 000 рублей за раз</p>
                                    </div>
                                    <div class="col-12 col-sm-6 cell">
                                        <i class="fad fa-lock-alt"></i>
                                        <h4>Таксую на велосипеде</h4>
                                        <p>Купить VIP привилегию</p>
                                    </div>
                                    <div class="col-12 col-sm-6 cell">
                                        <i class="fad fa-lock-alt"></i>
                                        <h4>Сын маминой подруги</h4>
                                        <p>Купить PREMIUM привилегию</p>
                                    </div>
                                    <div class="col-12 col-sm-6 cell">
                                        <i class="fad fa-lock-alt"></i>
                                        <h4>Племянник Билл Гейтса</h4>
                                        <p>Купить DELUXE привилегию</p>
                                    </div>
                                    <div class="col-12 col-sm-6 cell">
                                        <i class="fad fa-lock-alt"></i>
                                        <h4>Живу на Уолл Стрит</h4>
                                        <p>Купить LEGEND привилегию</p>
                                    </div>
                                    <div class="col-12 col-sm-6 cell">
                                        <i class="fad fa-lock-alt"></i>
                                        <h4>В магазин только на майбахе</h4>
                                        <p>Трижды продлить LEGEND привилегию</p>
                                    </div>
                                    <div class="col-12 col-sm-6 cell">
                                        <i class="fad fa-lock-alt"></i>
                                        <h4>Трачу и не плачу</h4>
                                        <p>Потратить на проекте в сумме 1 000 рублей</p>
                                    </div>
                                    <div class="col-12 col-sm-6 cell">
                                        <i class="fad fa-lock-alt"></i>
                                        <h4>Владелец заводов, газет, пароходов!</h4>
                                        <p>Потратить на проекте в сумме 10 000 рублей</p>
                                    </div>
                                    <div class="col-12 col-sm-6 cell">
                                        <i class="fad fa-lock-alt"></i>
                                        <h4>Продвинутый пользователь киви кошелька</h4>
                                        <p>Трижды пополнить свой баланс с QIWI</p>
                                    </div>
                                    <div class="col-12 col-sm-6 cell">
                                        <i class="fad fa-lock-alt"></i>
                                        <h4>Золотая лихорадка</h4>
                                        <p>Пополнить баланс аккаунта 10 раз за месяц (не менее чем на 1 000 рублей)</p>
                                    </div>
                                    <div class="col-12 col-sm-6 cell">
                                        <i class="fad fa-lock-alt"></i>
                                        <h4>Поднял бабла — школоло три топора</h4>
                                        <p>Накопить 1 000 рублей не пополняя счёта</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </b-modal>
        </div>
    </div>
</template>

<script>
    import api from '../api';
    import * as skinview3d from "../skinview3d.module";
    import {mapGetters} from "vuex";
    import Loader from "../components/Loader";
    import moment from "moment";

    export default {
        name: "UserProfile",
        components: { Loader },
        data(){
            return {
                loading: true,

                skinViewer: null,
                walk: null,

                profile: null,

                current_page: 1,

                modals:{
                    friends:{
                        loading: false,
                        open: false,
                        requests: []
                    },
                    achievements:{
                        loading: false,
                        open: false,
                        data: []
                    }
                }
            }
        },
        computed:{
            ...mapGetters(['user', 'isLogged']),
            myProfile(){
                if (!this.isLogged) return false;
                return this.user.id === this.profile.user.id;
            },
            isMyFriend () {
                if (!this.isLogged) return false;
                var found = false;
                var vm = this;
                this.profile.friends.forEach((friend) => {
                    if (friend.id === vm.user.id) found = true;
                });
                return found;
            }
        },
        mounted(){
            this.loadProfile();
        },
        methods:{
            reloadFriends(){
                api.get('user/' + this.$route.params.login).then(response => {
                    this.profile.friends = response.data.profile.friends;
                });
            },
            acceptFriend(id){
                api.post('friends/accept', {user: id}).then(response => {
                    if (response.data.success){
                        this.modals.friends.requests = this.modals.friends.requests.filter(function (request){
                            return request.sender_id !== id;
                        });
                        this.reloadFriends();
                    }
                });
            },
            denyFriend(id){
                api.post('friends/deny', {user: id}).then(response => {
                    if (response.data.success){
                        this.modals.friends.requests = this.modals.friends.requests.filter(function (request){
                            return request.sender_id !== id;
                        });
                    }
                });
            },
            openFriendsModal(){
                if (this.myProfile) this.modals.friends.loading = true;

                this.modals.friends.open = true;

                if (this.myProfile){
                    api.get('friends/requests')
                    .then(response => {
                        if (response.data.success){
                            this.modals.friends.requests = response.data.requests;
                        }

                        this.modals.friends.loading = false;
                    });
                }
            },
            sendFriendRequest(){
                api.post('friends/add', {user: this.profile.user.id});
                this.profile.has_friend_request = true;
            },
            removeFromFriend(){
                api.post('friends/remove', {user: this.profile.user.id}).then(() => {
                    this.reloadFriends();
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
            loadProfile(){
                this.loading = true;
                this.modals.friends.open = false;

                api.get('user/' + this.$route.params.login)
                .then(response => {
                    this.profile = response.data.profile;
                    this.loading = false;

                    // Count avg chars
                    this.profile.avg_chars = Math.round(this.profile.chars / this.profile.posts);
                    if (this.profile.posts < 1 || Number.isNaN(this.profile.avg_chars) || !Number.isFinite(this.profile.avg_chars)){
                      this.profile.avg_chars = 0;
                    }

                    this.$nextTick(function () {
                        this.initSkin();
                        this.initSpoilers();
                    });
                }).catch(error => {
                    console.log(error);
                });
            },
            initSkin(){
                if (!document.getElementById("user_viewer")) return;

                this.skinViewer = new skinview3d.SkinViewer({
                    domElement: document.getElementById("user_viewer"),
                    width: 280,
                    height: 560,
                    skinUrl: '/skins/' + this.profile.user.uuid + '.png',
                    capeUrl: '/cloaks/' + this.profile.user.uuid + '.png',
                    static: true
                });

                let control = skinview3d.createOrbitControls(this.skinViewer);
                control.enableRotate = false;
                control.enableZoom = false;
                control.enablePan = false;

                this.$set(this.skinViewer, 'animation', new skinview3d.CompositeAnimation());

                this.walk = this.skinViewer.animation.add(skinview3d.WalkingAnimation);
                this.walk.speed = 5;
                this.walk.paused = 5;

                this.skinViewer.playerObject.rotation._x = 0.15;
                this.skinViewer.playerObject.quaternion._y = 0.25;
            },
        },
        watch: {
            '$route': function () {
                this.loadProfile();
            },
            current_page: function (newVal) {
                this.loading = true;

                api.get('user/' + this.$route.params.login + '?page=' + newVal)
                    .then(response => {
                        this.profile.actions = response.data.profile.actions;
                        this.loading = false;
                        this.$nextTick(function () {
                            this.initSkin();
                            this.initSpoilers();
                        });
                    }).catch(error => {
                        console.log(error);
                    });
            }
        }
    }
</script>