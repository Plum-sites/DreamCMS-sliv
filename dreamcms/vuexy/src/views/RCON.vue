<template>
<!--    <v-container fluid grid-list-xl>-->
<!--        <notifications></notifications>-->
<!--        <v-layout row wrap>-->
<!--            <app-card-->
<!--                    heading="Отправить на все сервера"-->
<!--                    colClasses="xs12 sm6"-->
<!--            >-->
<!--                <v-text-field v-model="send.all.cmd" class="form-control" label="Команда" @keyup="allEnter"></v-text-field>-->
<!--                <v-btn block color="info" @click="sendAll(send.all.cmd)">Отправить</v-btn>-->
<!--            </app-card>-->

<!--            <app-card-->
<!--                    heading="Отправить на выбранные"-->
<!--                    colClasses="xs12 sm6"-->
<!--            >-->
<!--                <v-select multiple label="Выберите сервер" item-value="id" :items="servers" v-model="send.select.servers" item-text="name"></v-select>-->
<!--                <v-text-field v-model="send.select.cmd" class="form-control" label="Команда" @keyup="allEnter"></v-text-field>-->

<!--                <v-btn block color="info" @click="sendSelected(send.select.servers, send.select.cmd)">Отправить</v-btn>-->
<!--            </app-card>-->
<!--        </v-layout>-->

<!--        &lt;!&ndash;<v-layout row wrap>&ndash;&gt;-->
<!--            &lt;!&ndash;<app-card&ndash;&gt;-->
<!--                    &lt;!&ndash;heading="Консоль"&ndash;&gt;-->
<!--                    &lt;!&ndash;colClasses="xs12 sm12"&ndash;&gt;-->
<!--            &lt;!&ndash;&gt;&ndash;&gt;-->
<!--                &lt;!&ndash;<v-layout row wrap>&ndash;&gt;-->
<!--                    &lt;!&ndash;<server-console v-bind:key="server.id" :ref="'console' + server.id" v-for="server in servers" :server="server"></server-console>&ndash;&gt;-->
<!--                &lt;!&ndash;</v-layout>&ndash;&gt;-->
<!--            &lt;!&ndash;</app-card>&ndash;&gt;-->
<!--        &lt;!&ndash;</v-layout>&ndash;&gt;-->

<!--        <v-layout row wrap class="chat-layout">-->
<!--            <v-flex xl2 lg3 md3 sm0 xs0 class="chat-sidebar">-->
<!--                <v-card class="chat-content">-->
<!--                    <v-toolbar color="primary" dark>-->
<!--                        <v-toolbar-title>Сервера</v-toolbar-title>-->
<!--                    </v-toolbar>-->
<!--                    <v-list>-->
<!--                        <vue-perfect-scrollbar class="chat-sidebar-scroll" :style="getScrollHeight()" :settings="settings">-->
<!--                            <template v-for="server in servers">-->
<!--                                <v-list-tile-->
<!--                                        avatar-->
<!--                                        :key="server.id"-->
<!--                                        @click="openServer(server)"-->
<!--                                        :class="{'grayish-blue': selectedServer !== null && server.id === selectedServer.id }"-->
<!--                                >-->
<!--                                    <v-list-tile-avatar>-->
<!--                                        <v-icon>menu</v-icon>-->
<!--                                    </v-list-tile-avatar>-->
<!--                                    <v-list-tile-content>-->
<!--                                        <h6 class="mb-1" v-html="server.name"></h6>-->
<!--                                        <span class="fs-12 grey&#45;&#45;text fw-normal" v-if="messages[server.id] !== undefined && messages[server.id].length > 0" v-html="messages[server.id][messages[server.id].length - 1].msg"></span>-->
<!--                                    </v-list-tile-content>-->
<!--                                </v-list-tile>-->
<!--                            </template>-->
<!--                        </vue-perfect-scrollbar>-->
<!--                    </v-list>-->
<!--                </v-card>-->
<!--            </v-flex>-->

<!--            <v-flex xl10 lg9 md9 sm12 xs12 class="chat-main">-->
<!--                <div class="chat-wrapper">-->
<!--                    <template>-->
<!--                        <template v-if="selectedServer">-->
<!--                            <v-toolbar class="chat-head">-->
<!--                                <div class="chat-head-left d-custom-flex align-items-center">-->
<!--                                    <div class="media align-items-center">-->
<!--                                        <div class="media-body">-->
<!--                                            <h6 class="mb-0">{{ selectedServer.name }}</h6>-->
<!--                                        </div>-->
<!--                                    </div>-->
<!--                                </div>-->
<!--                            </v-toolbar>-->

<!--                            <vue-perfect-scrollbar :style="getScrollHeight()" class="chat-area-scroll" :settings="settings">-->
<!--                                <div class="chat-body">-->
<!--                                    <template v-for="message in messages[selectedServer.id]">-->
<!--                                        <div class="chat-block mb-3" :class="{'flex-row-reverse': message.side !== 'Server'}">-->
<!--                                            <template v-if="message.side === 'Server'">-->
<!--                                                <div class="chat-bubble-wrap">-->
<!--                                                    <div class="chat-bubble odd primary px-3 d-custom-flex align-items-center">-->
<!--                                                        <span class="d-inline-block fs-14"><span v-html="message.msg"></span></span>-->
<!--                                                    </div>-->
<!--                                                </div>-->
<!--                                            </template>-->
<!--                                            <template v-else>-->
<!--                                                <div class="chat-bubble-wrap">-->
<!--                                                    <div class="chat-bubble even aqua-bg px-3 d-custom-flex align-items-center">-->
<!--                                                        <span class="d-inline-block fs-14"><span v-html="message.msg"></span></span>-->
<!--                                                    </div>-->
<!--                                                </div>-->
<!--                                            </template>-->
<!--                                        </div>-->
<!--                                    </template>-->
<!--                                </div>-->
<!--                            </vue-perfect-scrollbar>-->

<!--                            <div class="chat-footer px-3">-->
<!--                                <div class="d-custom-flex">-->
<!--                                    <v-text-field-->
<!--                                            hide-details-->
<!--                                            name="input-1-3"-->
<!--                                            label="Send Message..."-->
<!--                                            single-line-->
<!--                                            :value="send.cmd"-->
<!--                                            v-model="send.cmd"-->
<!--                                            @keyup.enter="sendMessage"-->
<!--                                    ></v-text-field>-->
<!--                                    <v-btn fab class="chat-send-btn" dark small color="primary" @click="sendMessage">-->
<!--                                        <v-icon dark>send</v-icon>-->
<!--                                    </v-btn>-->
<!--                                </div>-->
<!--                            </div>-->
<!--                        </template>-->

<!--                        <div class="chat-box-main" v-else>-->
<!--                            <div class="centered">-->
<!--                                <p><i class="zmdi zmdi-comments font-3x primary&#45;&#45;text"></i></p>-->
<!--                                <v-btn class="select-user d-none" flat color="primary" @click="toggleChatSidebar">-->
<!--                                    Select User-->
<!--                                </v-btn>-->
<!--                            </div>-->
<!--                        </div>-->
<!--                    </template>-->
<!--                </div>-->
<!--            </v-flex>-->
<!--        </v-layout>-->
<!--    </v-container>-->


</template>

<script>
    import api from 'Api';

    import { mapGetters } from "vuex";
    import ServerConsole from "../components/ServerConsole";

    export default {
        name: "RCON",
        components: {ServerConsole},
        data: function () {
            return {
                selectedServer: null,

                send:{
                    cmd: '',
                    all:{
                        cmd: ''
                    },
                    select:{
                        servers: null,
                        cmd: ''
                    }
                },

                messages: [],

                settings: {
                    maxScrollbarLength: 160
                },
            }
        },
        computed:{
            ...mapGetters(["servers"])
        },
        mounted (){
            //this.$nextTick(() => {
                this.servers.forEach((server, index, arr) => {
                    this.$set(this.messages, server.id, []);

                    //this.messages[server.id] = [];
                });
            //})
        },
        methods:{
            sendMessage(){
                this.sendSelected([this.selectedServer.id], this.send.cmd);
            },
            openServer(server){
                this.selectedServer = server;
            },
            scrollToEnd() {
                var container = document.querySelector(".chat-area-scroll");
                if(container !== null){
                    var scrollHeight = container.scrollHeight;
                    container.scrollTop = scrollHeight;
                }
            },
            allEnter: function(event) {
                if(event.key == "Enter")
                {
                    this.sendAll(this.send.all.cmd);
                }
            },
            selectEnter: function(event) {
                if(event.key == "Enter")
                {
                    this.sendSelected(this.send.select.servers, this.send.select.cmd);
                }
            },
            sendAll: function(cmd){
                this.sendSelected(this.servers.map(server => server.id), cmd);
            },
            sendSelected: function(servers, cmd){
                servers.forEach((server, index, arr) => {
                    this.messages[server].push({
                        side: 'RCON',
                        msg: cmd,
                    });
                });

                api.post('rcon/send', {servers: servers, cmd: cmd})
                    .then(response => {
                        for(var serverid in response.data.messages) {
                            this.messages[serverid].push({
                                side: 'Server',
                                msg: response.data.messages[serverid],
                            });
                        }

                        this.scrollToEnd();
                    })
                    .catch(err => {
                        console.log(err);
                    });
            }
        }
    }
</script>