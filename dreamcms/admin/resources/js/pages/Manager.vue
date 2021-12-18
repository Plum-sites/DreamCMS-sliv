<template>
    <v-container fluid pt-0 grid-list-xl>
        <notifications></notifications>

        <section-tooltip title="Быстрый менеджер"></section-tooltip>

        <v-layout row wrap>
            <app-card
                    heading="Выбор игрока"
                    colClasses="xs12 sm6"
                    customClasses="mb-0 sales-widget"
            >
                <user-selector @selected="onSelectUser"></user-selector>
            </app-card>
        </v-layout>

        <v-layout row wrap v-if="loaded">
            <app-card
                    heading="Пользователь"
                    colClasses="xs12 sm3"
            >
                <div class="text-center">
                    <img class="profile-user-img img-fluid" :src="user.head_url">
                </div>

                <h3 class="profile-username text-center">{{ user.login }}</h3>

                <p class="text-muted text-center">{{ user.role }}</p>

                <ul class="list-group list-group-unbordered mb-3">
                    <li class="list-group-item">
                        <b>Рубли</b> <a class="float-right">{{ parseFloat(user.realmoney) + parseFloat(balance.rub) }}</a>

                        <v-text-field type="number" v-model="balance.rub" label="Изменение баланса"></v-text-field>
                    </li>
                    <li class="list-group-item">
                        <b>Монеты</b> <a class="float-right">{{ parseFloat(user.money) + parseFloat(balance.mon) }}</a>

                        <v-text-field type="number" v-model="balance.mon" label="Изменение баланса"></v-text-field>
                    </li>
                    <li class="list-group-item">
                        <b>Репутация</b> <a class="float-right">{{ parseInt(user.reputation) + parseInt(balance.rep) }}</a>

                        <v-text-field type="number" v-model="balance.rep" label="Изменение репутации"></v-text-field>
                    </li>
                </ul>

                <v-btn block color="warning" @click="updateBalance">Применить изменения</v-btn>
                <v-btn block color="info" @click="loadUserData(user)">Обновить</v-btn>
                <v-btn block color="error" @click="clear2FA" v-if="hasPermission('manager.2fa.delete')">Удалить 2FA</v-btn>
                <v-btn block color="error" @click="clearServerPerms" v-if="hasPermission('manager.permissions.revoke')">Очистить PEX на серверах</v-btn>
                <v-btn block color="error" @click="clearSitePerms" v-if="hasPermission('admin.acl.access')">Очистить права на сайте</v-btn>
            </app-card>

            <app-card
                    heading="Информация"
                    colClasses="xs12 sm9"
            >
                <v-tabs grow>
                    <v-tab>Донат</v-tab>
                    <v-tab>Корзина</v-tab>
                    <v-tab>Права</v-tab>
                    <v-tab>Блокировка</v-tab>

                    <v-tab-item>
                        <v-layout row wrap>
                            <app-card
                                    heading="Активные группы"
                                    colClasses="xs12 sm6"
                            >
                                <div class="v-table__overflow">
                                    <table class="v-datatable v-table">
                                        <tbody>
                                        <tr>
                                            <th>Сервер</th>
                                            <th>Группа</th>
                                            <th>Окончание</th>
                                            <th>Снятие</th>
                                        </tr>

                                        <tr v-for="status in user.status">
                                            <td>{{ status.server.name }}</td>
                                            <td>{{ status.group.name }}</td>
                                            <td>{{ formatUnix(status.expire) }}</td>
                                            <td><v-btn color="error" @click="removeDonate(status.id)" :disabled="!hasPermission('manager.donate.revoke')">X</v-btn></td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </app-card>

                            <app-card
                                    heading="Выдача доната"
                                    colClasses="xs12 sm6"
                            >

                                <v-select label="Выберите группу" item-value="id" :items="dgroups" v-model="donate.give.group" item-text="name"></v-select>
                                <v-select label="Выберите сервер" item-value="id" :items="servers" v-model="donate.give.server" item-text="name"></v-select>

                                <label>Срок действия:</label>
                                <date-range-picker id="donategive" time v-model="donate.give.time"></date-range-picker>

                                <v-btn block color="primary" @click="giveDonate" :disabled="!hasPermission('manager.donate.give')">Выдать</v-btn>
                            </app-card>
                        </v-layout>
                    </v-tab-item>

                    <v-tab-item>
                        <v-layout row wrap>
                            <app-card
                                    heading="Выдача предмета"
                                    colClasses="xs12 sm6"
                            >
                                <item-selector class="form-control" v-model="cart.give.item"></item-selector>
                                <v-select label="Выберите сервер" item-value="id" :items="servers" v-model="cart.give.server" item-text="name"></v-select>
                                <v-text-field type="number" label="Количество" v-model="cart.give.count"></v-text-field>

                                <v-btn block color="primary" @click="giveCartItem" :disabled="!hasPermission('manager.cart.give')">Выдать</v-btn>
                            </app-card>

                            <app-card
                                    heading="Выдача кита"
                                    colClasses="xs12 sm6"
                            >
                                <v-text-field label="Название кита" v-model="cart.kit_give.name"></v-text-field>
                                <v-select label="Выберите сервер" item-value="id" :items="servers" v-model="cart.kit_give.server" item-text="name"></v-select>

                                <v-btn block color="primary" @click="giveCartKit" :disabled="!hasPermission('manager.cart_kit.give')">Выдать</v-btn>
                            </app-card>
                        </v-layout>

                        <app-card
                                heading="Предметы в корзине"
                                colClasses="xs12 sm12"
                        >
                            <div class="v-table__overflow">
                                <table class="v-datatable v-table">
                                    <tbody>
                                    <tr>
                                        <th style="width: 10px">#</th>
                                        <th>ID магазина</th>
                                        <th>EnumName</th>
                                        <th>Damage</th>
                                        <th>Кол-во</th>
                                        <th>Чары</th>
                                        <th>NBT</th>
                                        <th v-if="hasPermission('manager.cart.revoke')">X</th>
                                    </tr>

                                    <tr v-for="cartitem in user.cart">
                                        <td>{{ cartitem.id }}</td>
                                        <td>{{ cartitem.shop }}</td>
                                        <td>{{ cartitem.type }}</td>
                                        <td>{{ cartitem.damage }}</td>
                                        <td>{{ cartitem.count }}</td>
                                        <td>{{ cartitem.enchants }}</td>
                                        <td>{{ cartitem.nbt }}</td>
                                        <td v-if="hasPermission('manager.cart.revoke')">
                                            <a class="btn btn-sm btn-default" v-on:click="removeCartItem(cartitem.id)"><i class="fa fa-remove"></i></a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </app-card>
                    </v-tab-item>

                    <v-tab-item>
                        <v-layout row wrap>
                            <app-card
                                    heading="Выдача группы"
                                    colClasses="xs12 sm6"
                            >
                                <v-text-field label="Название группы" v-model="group.give.group"></v-text-field>
                                <v-select label="Выберите сервер" item-value="id" :items="servers" v-model="group.give.server" item-text="name"></v-select>

                                <label>Срок действия</label>
                                <date-range-picker class="form-control" id="groupgive" time v-model="group.give.time"></date-range-picker>

                                <v-btn block color="primary" @click="giveGroup" :disabled="!hasPermission('manager.permissions.give')">Выдать</v-btn>
                            </app-card>

                            <app-card
                                    heading="Снятие группы"
                                    colClasses="xs12 sm6"
                            >
                                <v-text-field label="Название группы" v-model="group.revoke.group"></v-text-field>
                                <v-select label="Выберите сервер" item-value="id" :items="servers" v-model="group.revoke.server" item-text="name"></v-select>

                                <v-btn block color="primary" @click="revokeGroup" :disabled="!hasPermission('manager.permissions.revoke')">Снять</v-btn>
                            </app-card>
                        </v-layout>

                        <app-card
                                heading="Права на серверах"
                                colClasses="xs12 sm12"
                        >
                            <app-section-loader :state="user_pex.loading"></app-section-loader>
                            <v-btn block color="primary" @click="clearServerPerms" :disabled="!hasPermission('manager.permissions.revoke')">Снять права со всех серверов</v-btn>
                            <table class="v-datatable v-table" v-if="!user_pex.loading">
                                <tbody>
                                <tr>
                                    <th>Сервер</th>
                                    <th>Группа</th>
                                </tr>

                                <tr v-for="group in user_pex.list">
                                    <td>{{ group.server.name }}</td>
                                    <td>{{ group.name }}</td>
                                </tr>
                                </tbody>
                            </table>
                        </app-card>
                    </v-tab-item>

                    <v-tab-item>
                        <app-card
                                heading="Блокировки в игре"
                                colClasses="xs12 sm12"
                        >
                            <table class="v-datatable v-table">
                                <tbody>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>Причина</th>
                                    <th>Время</th>
                                    <th>Окончание</th>
                                    <th>Администратор</th>
                                    <th v-if="hasPermission('manager.ban_game.delete')"></th>
                                </tr>
                                <tr v-for="ban in user.bans.game">
                                    <td>{{ ban.id }}</td>
                                    <td>{{ ban.Reason }}</td>
                                    <td>{{ formatUnix(ban.Time) }}</td>
                                    <td>{{ban.Temptime ? formatUnix(ban.Temptime) : 'Навсегда' }}</td>
                                    <td>{{ ban.admin.login }}</td>
                                    <td v-if="hasPermission('manager.ban_game.delete')">
                                        <a class="btn btn-sm btn-danger" v-on:click="unbanGame(ban.id)"><i class="fa fa-remove"></i></a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </app-card>

                        <app-card
                                heading="Выдача блокировки в игре"
                                colClasses="xs12 sm12"
                        >
                            <v-text-field v-model="ban.game.reason" label="Причина"></v-text-field>
                            <v-select label="Выберите сервер" item-value="id" :items="servers" v-model="ban.game.server" item-text="name"></v-select>

                            <label>Срок действия:</label>
                            <date-range-picker class="form-control" id="gameban" time v-model="ban.game.time"></date-range-picker>

                            <v-btn block color="primary" @click="banGame" :disabled="!hasPermission('manager.ban_game.give')">Забанить</v-btn>
                        </app-card>

                        <app-card
                                heading="Блокировки на форуме"
                                colClasses="xs12 sm12"
                        >
                            <table class="v-datatable v-table">
                                <tbody>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>Причина</th>
                                    <th>Время</th>
                                    <th>Окончание</th>
                                    <th v-if="hasPermission('manager.ban_site.delete')"></th>
                                </tr>
                                <tr v-for="ban in user.bans.forum">
                                    <td>{{ ban.id }}</td>
                                    <td>{{ ban.comment }}</td>
                                    <td>{{ formatDate(ban.created_at) }}</td>
                                    <td>{{ formatDate(ban.expired_at) }}</td>
                                    <td v-if="hasPermission('manager.ban_site.delete')">
                                        <a class="btn btn-sm btn-danger" v-on:click="unbanForum(ban.id)"><i class="fa fa-remove"></i></a>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </app-card>

                        <app-card
                                heading="Выдача блокировки на форуме"
                                colClasses="xs12 sm12"
                        >
                            <v-text-field v-model="ban.forum.reason" label="Причина"></v-text-field>

                            <label>Срок действия:</label>
                            <date-range-picker class="form-control" id="forumban" time v-model="ban.forum.time"></date-range-picker>

                            <v-btn block color="primary" @click="banForum" :disabled="!hasPermission('manager.ban_site.give')">Забанить</v-btn>
                        </app-card>
                    </v-tab-item>
                </v-tabs>
            </app-card>
        </v-layout>
    </v-container>
</template>

<script>
    import { mapGetters } from "vuex";

    import UserSelector from "../components/UserSelector";
    import DateRangePicker from '../components/DateRangePicker';
    import ItemSelector from '../components/ItemSelector';

    import api from 'Api';
    import moment from 'moment';

    export default {
        name: "ManageUser",
        components: {UserSelector, DateRangePicker, ItemSelector},
        data: function () {
            return {
                user: null,
                loaded: false,
                user_pex:{
                    list: [],
                    loading: true
                },
                balance:{
                    rub: 0,
                    mon: 0,
                    rep: 0
                },
                donate: {
                    give: {
                        server: null,
                        group: null,
                        time: null
                    }
                },
                cart: {
                    give: {
                        server: null,
                        count: 0,
                        item: null
                    },
                    kit_give: {
                        name: null,
                        server: null
                    }
                },
                group: {
                    give: {
                        server: null,
                        group: null,
                        time: null
                    },
                    revoke: {
                        server: null,
                        group: null
                    }
                },
                ban:{
                    game:{
                        server: null,
                        reason: null,
                        time: null
                    },
                    forum:{
                        reason: null,
                        time: null
                    }
                }
            }
        },
        computed:{
            ...mapGetters(["servers", "dgroups"])
        },
        methods:{
            formatUnix(timestamp){
                return moment.unix(timestamp).format('lll');
            },
            formatDate(date){
                return moment(date).format('lll');
            },
            findById(array, id){
                return array.find(obj => obj.id === parseInt(id));
            },
            onSelectUser(user) {
                this.loaded = false;

                this.user = user;

                if (user != null) {
                    this.loadUserData(user);
                }
            },
            loadUserData(user){
                api.post('manager/user', {id: user.id})
                    .then(response => {
                        this.user = response.data.user;
                        this.user.cart = response.data.cart;
                        this.user.bans = response.data.bans;
                        this.user.status = response.data.status;
                        this.user.groups = [];

                        response.data.status.map((value, key) => {
                            value.group = this.findById(this.dgroups, value.group_id);
                            value.server = this.findById(this.servers, value.server_id);
                            console.log(value);
                            this.user.groups.push(value);
                        });

                        console.log(this.user);

                        this.user_pex.loading = true;
                        this.loaded = true;

                        api.post('manager/user/pex', {id: user.id})
                            .then(response => {
                                this.user_pex.list = response.data.groups;

                                this.user_pex.list.map((value, key) => {
                                    value.server_id = value.server;
                                    value.server = this.findById(this.servers, value.server);
                                    return value;
                                });

                                this.user_pex.loading = false;
                            })
                            .catch(err => {
                                console.log(err);
                                App.showError(err.message);
                            });
                    })
                    .catch(err => {
                        console.log(err);
                        App.showError(err.message);
                    });
            },
            sendRequest(url, data, callback){
                api.post(url, data)
                    .then(response => {
                        if (callback !== null){
                            callback(response);
                        }
                    })
                    .catch(err => {
                        console.log(err);
                    });
            },
            updateBalance(){
                this.sendRequest('manager/user/balance', {user: this.user.id, realmoney: this.balance.rub, money: this.balance.mon, reputation: this.balance.rep},
                    () => {
                        this.balance.rub = 0;
                        this.balance.mon = 0;
                        this.balance.rep = 0;
                        this.loadUserData(this.user);
                    }
                );
            },
            momentTotimespamps(m){
                return {
                    start: moment(m.start).unix(),
                    end: moment(m.end).unix()
                };
            },
            giveDonate(){
                this.sendRequest('manager/donate/give', {user: this.user.id, server: this.donate.give.server, group: this.donate.give.group, time: this.momentTotimespamps(this.donate.give.time)});
            },
            removeDonate(id){
                this.sendRequest('manager/donate/remove', {id: id});
            },
            giveGroup() {
                this.sendRequest('manager/pex/group/add', {user: this.user.id, server: this.group.give.server, group: this.group.give.group, time: this.momentTotimespamps(this.group.give.time)});
            },
            revokeGroup(){
                this.sendRequest('manager/pex/group/remove', {user: this.user.id, server: this.group.revoke.server, group: this.group.revoke.group});
            },
            giveCartItem(){
                this.sendRequest('manager/cart/give', {user: this.user.id, server: this.cart.give.server, count: this.cart.give.count, item: this.cart.give.item.id});
            },
            giveCartKit(){
                this.sendRequest('manager/cart/kit', {user: this.user.id, server: this.cart.kit_give.server, name: this.cart.kit_give.name});
            },
            removeCartItem(id){
                this.sendRequest('manager/cart/remove', {id: id});
            },
            banGame(){
                this.sendRequest('manager/ban/game', {user: this.user.id, reason: this.ban.game.reason, time: this.momentTotimespamps(this.ban.game.time), server: this.ban.game.server});
            },
            banForum(){
                this.sendRequest('manager/ban/forum', {user: this.user.id, reason: this.ban.forum.reason, time: this.momentTotimespamps(this.ban.forum.time)});
            },
            unbanGame(id){
                this.sendRequest('manager/unban/game', {id: id});
            },
            unbanForum(id){
                this.sendRequest('manager/unban/forum', {user: this.user.id});
            },
            clearServerPerms(){
                this.sendRequest('manager/pex/clear', {user: this.user.id});
            },
            clearSitePerms(){
                this.sendRequest('manager/perms/clear', {user: this.user.id});
            },
            loginAsUser(){
                this.sendRequest('manager/login', {user: this.user.id});
            },
            clear2FA(){
                this.sendRequest('manager/user/2fa', {user: this.user.id});
            }
        },

        watch: {
            'donate.give.time': function (val) {
                console.log(val);
            }
        }
    }
</script>