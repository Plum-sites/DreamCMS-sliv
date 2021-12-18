<template>
    <div>

    </div>
<!--    <v-container fluid pt-0 grid-list-xl>-->
<!--        <notifications></notifications>-->

<!--        <section-tooltip title="Форумный менеджер"></section-tooltip>-->

<!--        <v-layout row wrap>-->
<!--            <app-card-->
<!--                    heading="Выбор игрока"-->
<!--                    colClasses="xs12 sm6"-->
<!--                    customClasses="mb-0 sales-widget"-->
<!--            >-->
<!--                <user-selector @selected="onSelectUser"></user-selector>-->
<!--            </app-card>-->

<!--            <app-card-->
<!--                    heading="Выбор темы"-->
<!--                    colClasses="xs12 sm6"-->
<!--                    customClasses="mb-0 sales-widget"-->
<!--            >-->
<!--                <v-text-field type="text" v-model="discussion.url" label="Ссылка на тему"></v-text-field>-->
<!--                <v-btn color="info" @click="loadDiscussion">Поиск</v-btn>-->
<!--            </app-card>-->
<!--        </v-layout>-->

<!--        <v-layout row wrap v-if="loaded">-->
<!--            <app-card-->
<!--                    heading="Пользователь"-->
<!--                    colClasses="xs12 sm3"-->
<!--            >-->
<!--                <div class="text-center">-->
<!--                    <img class="profile-user-img img-fluid" :src="user.head_url">-->
<!--                </div>-->

<!--                <h3 class="profile-username text-center">{{ user.login }}</h3>-->

<!--                <p class="text-muted text-center">{{ user.role }}</p>-->
<!--            </app-card>-->

<!--            <app-card-->
<!--                    heading="Информация"-->
<!--                    colClasses="xs12 sm9"-->
<!--            >-->
<!--                <v-tabs grow>-->
<!--                    <v-tab>Блокировка</v-tab>-->
<!--                    <v-tab v-if="hasPermission('forum_manager.logs.access')">Логи</v-tab>-->
<!--                    <v-tab>Подпись</v-tab>-->
<!--                    <v-tab-item>-->
<!--                        <v-layout row wrap>-->
<!--                            <app-card-->
<!--                                    heading="Блокировки на форуме"-->
<!--                                    colClasses="xs12 sm6"-->
<!--                            >-->
<!--                                <table class="v-datatable v-table">-->
<!--                                    <thead>-->
<!--                                        <tr>-->
<!--                                            <th>Причина</th>-->
<!--                                            <th>Время</th>-->
<!--                                            <th>Окончание</th>-->
<!--                                            <th v-if="hasPermission('forum_manager.ban.access')">Снятие</th>-->
<!--                                        </tr>-->
<!--                                    </thead>-->
<!--                                    <tbody>-->
<!--                                        <tr v-for="ban in user.bans.forum">-->
<!--                                            <td>{{ ban.comment }}</td>-->
<!--                                            <td>{{ formatDate(ban.created_at) }}</td>-->
<!--                                            <td>{{ formatDate(ban.expired_at) }}</td>-->
<!--                                            <td v-if="hasPermission('forum_manager.ban.access')">-->
<!--                                                <v-btn icon class="mx-0" @click="unbanForum(ban.id)" >-->
<!--                                                    <v-icon color="grey lighten-1" >close</v-icon>-->
<!--                                                </v-btn>-->
<!--                                            </td>-->
<!--                                        </tr>-->
<!--                                    </tbody>-->
<!--                                </table>-->
<!--                            </app-card>-->

<!--                            <app-card v-if="hasPermission('forum_manager.ban.access')"-->
<!--                                    heading="Выдача блокировки форума"-->
<!--                                    colClasses="xs12 sm6"-->
<!--                            >-->
<!--                                <v-form>-->
<!--                                    <v-text-field label="Причина" v-model="ban.forum.reason"></v-text-field>-->
<!--                                    <date-range-picker class="form-control" id="forumban" time-->
<!--                                                       v-model="ban.forum.time"></date-range-picker>-->
<!--                                    <v-btn color="error" @click="banForum">Выдать блокировку</v-btn>-->
<!--                                </v-form>-->
<!--                            </app-card>-->
<!--                        </v-layout>-->
<!--                    </v-tab-item>-->
<!--                    <v-tab-item v-if="hasPermission('forum_manager.logs.access')">-->
<!--                        <app-card-->
<!--                                heading="Действия пользователя на форуме"-->
<!--                                colClasses="xs12 sm12"-->
<!--                        >-->
<!--                            <v-timeline dense="">-->
<!--                                <v-timeline-item v-for="audit in user_logs.logs" :key="audit.id" :icon="'zmdi ' + (audit.event === 'deleted' ? 'zmdi-close' : (audit.event === 'created' ? 'zmdi-open-in-new' : 'zmdi-comment-outline'))">-->
<!--                                    <v-card-->
<!--                                            :color="audit.event === 'deleted' ? 'error' : (audit.event === 'created' ? 'success' : 'warning')"-->
<!--                                            dark-->
<!--                                    >-->
<!--                                        <v-card-title class="title">{{ audit.auditable_type }} {{ audit.event }}</v-card-title>-->
<!--                                        <v-card-text>-->
<!--                                            <code>-->
<!--                                                {{ audit.old_values }}-->
<!--                                            </code>-->
<!--                                            <h2>-></h2>-->
<!--                                            <code>-->
<!--                                                {{ audit.new_values }}-->
<!--                                            </code>-->
<!--                                            <br>-->
<!--                                            <a :href="audit.url">{{ audit.url }}</a>-->
<!--                                            <br>-->
<!--                                            <p>IP адрес: <a target="_blank"-->
<!--                                                            :href="`https://2ip.ru/whois/?ip=${audit.ip_address}`">{{-->
<!--                                                audit.ip_address }}</a></p>-->
<!--                                            <br>-->
<!--                                            <p>Браузер: {{ audit.user_agent }}</p>-->
<!--                                        </v-card-text>-->
<!--                                    </v-card>-->
<!--                                </v-timeline-item>-->
<!--                            </v-timeline>-->

<!--                            <app-section-loader :status="!user_logs.loaded"></app-section-loader>-->

<!--                            <v-btn @click="loadUserLogs">Загрузить еще</v-btn>-->
<!--                        </app-card>-->
<!--                    </v-tab-item>-->
<!--                    <v-tab-item>-->
<!--                        <app-card-->
<!--                                heading="Подпись игрока"-->
<!--                                colClasses="xs12 sm12"-->
<!--                        >-->
<!--                            <code>-->
<!--                                {{ user.sign }}-->
<!--                            </code>-->

<!--                            <p v-html="user.sign"></p>-->

<!--                            <v-btn color="error" @click="removeSign" :disabled="!hasPermission('forum_manager.sign.delete')">Удалить подпись</v-btn>-->
<!--                        </app-card>-->
<!--                    </v-tab-item>-->
<!--                </v-tabs>-->
<!--            </app-card>-->
<!--        </v-layout>-->

<!--        <v-layout row wrap v-if="discussion.loaded">-->
<!--            <app-card-->
<!--                    heading="Тема"-->
<!--                    colClasses="xs12 sm3"-->
<!--            >-->
<!--                <h3 class="text-center">{{ discussion.selected.title }}</h3>-->
<!--                <h4 class="text-center">{{ discussion.selected.slug }}</h4>-->
<!--            </app-card>-->

<!--            <app-card-->
<!--                    heading="Действия в этой теме"-->
<!--                    colClasses="xs12 sm12"-->
<!--            >-->
<!--                <v-timeline dense>-->
<!--                    <v-timeline-item v-for="audit in discussion_logs.logs" :key="audit.id" :icon="'zmdi ' + (audit.event === 'deleted' ? 'zmdi-close' : (audit.event === 'created' ? 'zmdi-open-in-new' : 'zmdi-comment-outline'))">-->
<!--                        <v-card-->
<!--                                :color="audit.event === 'deleted' ? 'error' : (audit.event === 'created' ? 'success' : 'warning')"-->
<!--                                dark-->
<!--                        >-->
<!--                            <v-card-title class="title">{{ audit.auditable_type }} {{ audit.event }}</v-card-title>-->
<!--                            <v-card-text>-->
<!--                                <code>-->
<!--                                    {{ audit.old_values }}-->
<!--                                </code>-->
<!--                                <h2>-></h2>-->
<!--                                <code>-->
<!--                                    {{ audit.new_values }}-->
<!--                                </code>-->
<!--                                <br>-->
<!--                                <a :href="audit.url">{{ audit.url }}</a>-->
<!--                                <br>-->
<!--                                <p>IP адрес: <a target="_blank"-->
<!--                                                :href="`https://2ip.ru/whois/?ip=${audit.ip_address}`">{{-->
<!--                                    audit.ip_address }}</a></p>-->
<!--                                <br>-->
<!--                                <p>Браузер: {{ audit.user_agent }}</p>-->
<!--                            </v-card-text>-->
<!--                        </v-card>-->
<!--                    </v-timeline-item>-->
<!--                </v-timeline>-->

<!--                <app-section-loader :status="!discussion_logs.loaded"></app-section-loader>-->

<!--                <v-btn @click="loadDiscussionLogs">Загрузить еще</v-btn>-->
<!--            </app-card>-->
<!--        </v-layout>-->
<!--    </v-container>-->
</template>

<script>
    import UserSelector from "../components/UserSelector";
    import api from "../api";
    import moment from 'moment';

    export default {
        name: "ManageUser",
        components: { UserSelector },
        data: function () {
            return {
                user: null,
                loaded: false,
                user_logs: {
                    last: 0,
                    logs: [],
                    loaded: false
                },
                discussion: {
                    loaded: false,
                    url: null,
                    selected: null
                },
                discussion_logs: {
                    last: 0,
                    logs: [],
                    loaded: false
                },
                ban: {
                    forum: {
                        reason: null,
                        time: null
                    }
                }
            }
        },
        methods: {
            formatDate(date) {
                return moment(date).format('DD-MM-YYYY');
            },
            findById(array, id) {
                return array.find(obj => obj.id === parseInt(id));
            },
            onSelectUser(user) {
                this.loaded = false;

                this.user = user;

                if (user != null) {
                    this.loadUserData(user);
                }
            },
            loadDiscussion() {
                this.discussion.loaded = false;

                this.discussion_logs.last = 0;
                this.discussion_logs.logs = [];

                var slug = this.discussion.url;

                if (slug.lastIndexOf('/') > -1) {
                    slug = slug.substring(slug.lastIndexOf('/') + 1);
                }

                api.post('forum/manager/discussion', {slug: slug})
                    .then(response => {
                        this.discussion.selected = response.data.discussion;

                        console.log(this.discussion);

                        this.discussion.loaded = true;

                        this.loadDiscussionLogs();
                    }).catch(err => {
                    console.log(err);
                });
            },
            loadUserData(user) {
                api.post('forum/manager/user', {id: user.id})
                    .then(response => {
                        this.user = response.data.user;
                        this.user.bans = response.data.bans;

                        console.log(this.user);

                        this.loaded = true;

                        //TODO
                        //if (vm.hasPermission('forum_manager.logs.access'))
                            this.loadUserLogs();
                    }).catch(err => {
                    console.log(err);
                });
            },
            loadUserLogs() {
                this.user_logs.loaded = false;

                api.post('forum/manager/user/logs', {user: this.user.id, last: this.user_logs.last})
                    .then(response => {
                        if (response.data.length > 0){
                            this.user_logs.last = response.data[response.data.length - 1].id;

                            response.data.forEach((val, index, arr) => {
                                val.old_values = JSON.parse(val.old_values);
                                val.new_values = JSON.parse(val.new_values);
                                arr[index] = val;
                            });

                            this.user_logs.logs = this.user_logs.logs.concat(response.data);

                            console.log(this.user_logs.logs);
                        }

                        this.user_logs.loaded = true;

                    }).catch(err => {
                    console.log(err);
                });
            },
            loadDiscussionLogs() {
                this.discussion_logs.loaded = false;
                api.post('forum/manager/discussion/logs', {
                    id: this.discussion.selected.id,
                    last: this.discussion_logs.last
                })
                    .then(response => {
                        console.log(response.data);

                        if (response.data.length > 0){
                            this.discussion_logs.last = response.data[response.data.length - 1].id;

                            response.data.forEach((val, index, arr) => {
                                val.old_values = JSON.parse(val.old_values);
                                val.new_values = JSON.parse(val.new_values);
                                arr[index] = val;
                            });

                            this.discussion_logs.logs = this.discussion_logs.logs.concat(response.data);

                            console.log(this.discussion_logs.logs);
                        }

                        this.discussion_logs.loaded = true;
                    }).catch(err => {
                        console.log(err);
                    });
            },
            banForum() {
                api.post('forum/manager/ban/forum', {
                    user: this.user.id,
                    reason: this.ban.forum.reason,
                    time: moment(this.ban.forum.time.end).unix()
                })
                .then(response => {
                    this.$notify({
                        type: 'success',
                        title: 'Успешно',
                        text: 'Вы выдали блокировку'
                    });
                }).catch(err => {
                    this.$notify({
                        type: 'error',
                        title: 'Ошибка',
                        text: 'Ошибка при выполнении запроса!'
                    });
                });
            },
            unbanForum(id) {
                api.post('forum/manager/unban/forum', { user: this.user.id })
                    .then(response => {
                        this.$notify({
                            type: 'success',
                            title: 'Успешно',
                            text: 'Вы сняли все блокировки игрока на форуме'
                        });
                    }).catch(err => {
                        this.$notify({
                            type: 'error',
                            title: 'Ошибка',
                            text: 'Ошибка при выполнении запроса!'
                        });
                    });

                App.sendRequest('/admin/fmanager/unban/forum', {user: this.user.id});
            },
            removeSign() {
                api.post('forum/manager/user/sign/remove', { user: this.user.id })
                    .then(response => {
                        this.$notify({
                            type: 'success',
                            title: 'Успешно',
                            text: 'Вы удалили подпись игрока'
                        });
                    }).catch(err => {
                    this.$notify({
                        type: 'error',
                        title: 'Ошибка',
                        text: 'Ошибка при выполнении запроса!'
                    });
                });
            }
        }
    }
</script>