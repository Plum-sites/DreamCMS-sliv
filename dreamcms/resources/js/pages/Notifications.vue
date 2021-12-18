<template>
    <div class="inner">
        <div class="headline">
            <h2>Центр уведомлений</h2>
            <p>Здесь отображаются все Ваши уведомления о тех или иных событиях и действиях. Вам отправили заявку в друзья, либо же кому-то понравилось Ваше сообщение? Мы незамедлительно уведомим Вас, будьте в курсе всего первым!</p>
        </div>
        <loader v-if="this.loading"></loader>
        <div class="notice_center" v-else>
            <ul class="notice_content">
                <li :class="getNotifyClass(notify)" v-for="notify in notifications">
                    <span v-if="notify.type === 'App\\Notifications\\FriendRequest' && notify.data.action === 'befriend'">
                        <router-link :to="{name: 'user', params: {login: getNotMe(notify).login}}">
                            <div class="user_pic">
                                <img :src="getHeadUrl(getNotMe(notify).uuid)" alt>
                            </div>
                            {{ getNotMe(notify).login }}
                        </router-link> отправил Вам заявку в друзья
                    </span>

                    <span v-if="notify.type === 'App\\Notifications\\FriendRequest' && notify.data.action === 'unfriend'">
                        <router-link :to="{name: 'user', params: {login: getNotMe(notify).login}}">
                            <div class="user_pic">
                                <img :src="getHeadUrl(getNotMe(notify).uuid)" alt>
                            </div>
                            {{ getNotMe(notify).login }}
                        </router-link> удалил вас из друзей
                    </span>

                    <span v-if="notify.type === 'App\\Notifications\\FriendRequest' && notify.data.action === 'accept'">
                        <router-link :to="{name: 'user', params: {login: getNotMe(notify).login}}">
                            <div class="user_pic">
                                <img :src="getHeadUrl(getNotMe(notify).uuid)" alt>
                            </div>
                            {{ getNotMe(notify).login }}
                        </router-link> принял ваш запрос в друзья
                    </span>

                    <span v-if="notify.type === 'App\\Notifications\\FriendRequest' && notify.data.action === 'deny'">
                        <router-link :to="{name: 'user', params: {login: getNotMe(notify).login}}">
                            <div class="user_pic">
                                <img :src="getHeadUrl(getNotMe(notify).uuid)" alt>
                            </div>
                            {{ getNotMe(notify).login }}
                        </router-link> отклонил запрос на дружбу
                    </span>


                    <span v-if="notify.type === 'App\\Notifications\\NewReply'">
                        <router-link :to="{name: 'user', params: {login: notify.data.user.login}}">
                            <div class="user_pic">
                                <img :src="getHeadUrl(notify.data.user.uuid)" alt>
                            </div>
                            {{ notify.data.user.login }}
                        </router-link>
                        ответил в Вашей теме
                        <router-link :to="{name: 'discussion', params: {slug: notify.data.discussion.slug }}">
                            {{ notify.data.discussion.title }}
                        </router-link>
                    </span>

                    <span v-if="notify.type === 'App\\Notifications\\BalanceAdd'">
                        Ваш баланс успешно пополнен на {{ notify.data.sum }} {{ declOfNum(notify.data.sum, ['стрим', 'стрима', 'стримов']) }}!
                    </span>

                    <span v-if="notify.type === 'App\\Notifications\\ChangePasswordNotify'">
                        Успешная смена пароля [IP: {{ notify.data.ip }}]
                    </span>

                    <span v-if="notify.type === 'App\\Notifications\\TextNotification'">
                        <router-link :to="{ name: notify.data.route }" v-if="notify.data.route">
                            {{ notify.data.text }}
                        </router-link>
                        <div v-else>
                          {{ notify.data.text }}
                        </div>
                    </span>

                    <span class="meta">{{ humanDiff(notify.created_at) }} назад</span>
                </li>
            </ul>
            <div class="paging" v-if="pagination.total >= pagination.per_page">
                <b-pagination v-model="pagination.current_page"
                              :total-rows="pagination.total"
                              :per-page="pagination.per_page">
                </b-pagination>
            </div>
        </div>
    </div>
</template>

<script>
    import api from '../api';
    import {mapGetters} from "vuex";
    import Loader from "../components/Loader";

    export default {
        name: "Notifications",
        components: {Loader},
        data() {
            return {
                loading: true,
                notifications: [],
                pagination: {
                    current_page: 1,
                    last_page: 1,
                    per_page: 10,
                    total: 1,
                }
            }
        },
        mounted(){
            this.load();
        },
        computed:{
          ...mapGetters(['user'])
        },
        methods:{
            load(){
                this.loading = true;

                api.get('core/notifications?page=' + this.pagination.current_page).then(response => {
                    if (response.data.success){
                        this.pagination.current_page = response.data.notifications.current_page;
                        this.pagination.last_page = response.data.notifications.last_page;
                        this.pagination.per_page = response.data.notifications.per_page;
                        this.pagination.total = response.data.notifications.total;

                        this.notifications = [];
                        this.notifications = this.notifications.concat(response.data.unread);
                        this.notifications = this.notifications.concat(response.data.notifications.data);

                        this.loading = false;
                    }
                });
            },
            getNotMe(notify){
                if (notify.data.friendship.sender.id !== this.user.id){
                    return notify.data.friendship.sender;
                }
                return notify.data.friendship.recipient;
            },
            getNotifyClass(notify){
                var clazz = '';

                if (notify.type === 'App\\Notifications\\FriendRequest') {
                    switch (notify.data.action) {
                        case 'befriend':
                            clazz = 'request';
                            break;
                        case 'unfriend':
                            clazz = 'remove';
                            break;
                        default:
                            clazz = notify.data.action;
                            break;
                    }
                }

                if (notify.type === 'App\\Notifications\\NewReply') {
                    clazz = 'answer';
                }

                if (notify.type === 'App\\Notifications\\ChangePasswordNotify') {
                    clazz = 'changepass';
                }

                if (notify.type === 'App\\Notifications\\BalanceAdd') {
                    clazz = 'plus';
                }

                if (notify.type === 'App\\Notifications\\TextNotification') {
                    clazz = 'start';
                }

                if (!notify.read_at){
                    clazz += ' unread';
                }

                return clazz;
            }
        },
        watch: {
            'pagination.current_page': function (val) {
                this.load();
            }
        }
    }
</script>
