<template>
    <div id="middle">
        <div class="wrapper">
            <div class="row">
                <div class="col mr-lg-4">
                    <router-view></router-view>
                </div>
                <div class="col-12 col-md-6 col-lg-5 col-xl-3 pl-lg-0">
                    <router-link :to="{name: 'page', params: {name: 'download'}}" class="btn_large primary mb-3" v-if="isLogged">
                        <span>Скачать лаунчер</span>
                        <small>Начни игру всего в пару кликов!</small>
                    </router-link>
                    <router-link :to="{name: 'page', params: {name: 'start'}}" class="btn_large success mb-3" v-else>
                        <span>Начать играть!</span>
                        <small>Начни игру всего в пару кликов!</small>
                    </router-link>
                    <div class="quarter mt-5">
                        <h3>Наши сервера</h3>
                        <div class="content servers">
                            <ul>
                                <li v-for="(branch, name) in branches">
                                    <a href="#" class="btn_common" @click.prevent="selected && selected === name ? selected = false : selected = name">
                                        <div class="heading">
                                            <div class="h4">
                                                {{ name }}
                                                <small v-if="branch.maxonline > 0">{{ branch.servers.length }} {{ declOfNum(branch.servers.length, ['сервер', 'сервера', 'серверов']) }} [v{{ branch.version}}]</small>
                                                <small v-else>Недоступен</small>
                                            </div>
                                            <div class="online" v-if="branch.maxonline > 0">
                                                <b>{{ branch.online }}</b> из {{ branch.maxonline }}
                                            </div>
<!--                                            <div class="online" v-else>-->
<!--                                                <b>N/A</b>-->
<!--                                            </div>-->
                                        </div>
                                        <div class="branches" :style="'display: ' + (selected && selected === name ? 'block' : 'none') + ';'">
                                            <div class="row align-items-center" v-for="server in branch.servers">
                                                <div class="h5">{{ server.name }}</div>
                                                <div class="lot" v-if="server.maxonline > 0">{{ server.online }}</div>
                                                <div class="lot" v-else>N/A</div>
                                                <div class="col-12 bar">
                                                    <div class="process" :style="'width: ' + ((server.online / server.maxonline) * 100) + '%;'"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                            <div class="row total align-items-center">
                                <h5 class="col-6 pr-1">
                                    {{ readableNum(totalOnline) }}
                                    <small>Всего игроков</small>
                                </h5>
                                <h5 class="col-6 pl-1">
                                    {{ readableNum(Math.max(record, totalOnline)) }}
                                    <small>Рекорд дня</small>
                                </h5>
                            </div>
                        </div>
                    </div>
                    <div class="quarter mt-5">
                        <div class="content">
                            <h4>Последние ответы</h4>
                            <ul>
                                <li v-for="post in lastPosts">
                                    <router-link :to="{name: 'discussion', params: {slug: post.discussion.slug}}" class="small">{{ post.discussion.title }}</router-link>
                                    <router-link :to="{name: 'discussion', params: {slug: post.discussion.slug}}" class="title">{{ stripHTML(post.body) }}</router-link>
                                    <div class="small">От <router-link :to="{name: 'user', params: {login: post.user.login}}">{{ post.user.login }}</router-link> {{ humanDiff(post.created_at) }} назад</div>
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
    import { mapGetters } from 'vuex';
    //import moment from "moment";
    //import api, {FORUM_LOAD_LATEST} from './api';

    export default {
        name: "Container",
        data(){
            return {
                selected: false,
            }
        },
        created(){
            this.$store.dispatch(FORUM_LOAD_LATEST);
        },
        methods:{
            stripHTML(msg){
                let regex = /(<([^>]+)>)/ig;

                var string = msg.replace(regex, "");
                var trimmedString = string.length > 96 ?
                    string.substring(0, 93) + "..." :
                    string;

                return trimmedString;
            },
        },
        computed: {
            ...mapGetters(['isLogged', 'servers', 'lastPosts', 'record']),
            branches(){
                var branches = {};
                for (const [id, serv] of Object.entries(this.servers)){
                    var branch = serv.name.split(' ')[0];
                    branches[branch] = {online: 0, maxonline: 0, servers: []};
                }
                for (const [id, serv] of Object.entries(this.servers)){
                    var branch = serv.name.split(' ')[0];
                    branches[branch].online += serv.online;
                    branches[branch].maxonline += serv.maxonline;
                    branches[branch].version = serv.version;
                    branches[branch].servers.push(serv);
                }
                return branches;
            },
            totalOnline(){
                var online = 0;
                for (const [name, branch] of Object.entries(this.branches)){
                    branch.servers.forEach(function (server) {
                        online += server.online;
                    });
                }
                return online;
            }
        }
    }
</script>

<style scoped>

</style>