<template>
    <div class="chat">
        <div class="row h-100 m-0">
            <div class="col h-100 px-0">
                <div class="chat_window">
                    <ul class="chat_content">
                        <li v-for="msg in messages">
                            <router-link :to="{name: 'user', params: {login: msg.user.login}}" class="user_pic">
                                <img :src="getHeadUrl(msg.user.uuid)">
                            </router-link>
                            <div class="msg_body">
                                <router-link :to="{name: 'user', params: {login: msg.user.login}}">{{ msg.user.login }}</router-link> <small v-html="msg.user.role"></small>
                                <p v-html="parseMsg(msg.text)"></p>
                            </div>
                            <div class="msg_meta">
                                <a href="#" v-if="isModer" @click="deleteMsg(msg.text)">Удалить</a>
                                {{ msg.time }}
                            </div>
                        </li>
                    </ul>
                    <div :class="'lower ' + (emoji ? 'emoji_visible' : '')">
                        <div class="emoji_wrap">
                            <div class="emoji_scroll">
                                <div class="item" v-if="recentEmoji && recentEmoji.length > 0">
                                    <h4><span>Часто используемые</span></h4>
                                    <a href="#" v-for="id in recentEmoji" @click.prevent="putEmoji(id, false)">
                                        <i :class="'emoji emoji_' + id"></i>
                                    </a>
                                </div>
                                <div class="item">
                                    <h4><span>Эмоции & Жесты</span></h4>
                                    <a href="#" v-for="id in 58" @click.prevent="putEmoji(id)">
                                        <i :class="'emoji emoji_' + id"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="chat_enter" v-if="this.isLogged">
                            <a href="#" class="btn_emoji" @click.prevent="emoji = !emoji">
                                <i class="fal fa-smile"></i>
                            </a>
                            <input title="Ввод сообщения" placeholder="Введите сообщение" autocomplete="off" v-model="msg" @keypress.enter="send">
                            <a href="#" class="btn_common ml-2 d-none d-sm-inline-block" @click.prevent="send">Отправить</a>
                        </div>
                    </div>
                </div>
                <div class="chat_resize"></div>
            </div>
        </div>
    </div>
</template>

<script>
    import $ from 'jquery';
    import {mapGetters} from "vuex";

    export default {
        name: "ForumChat",
        computed:{
            ...mapGetters(['isLogged'])
        },
        data(){
           return  {
               recentEmoji: localStorage.getItem('recentEmoji') ? this.loadRecent(localStorage.getItem('recentEmoji')) : [],
               emoji: false,
               msg: '',

               isModer: false,

               messages: []
           }
        },
        mounted(){
            let chat = $('.chat');

            chat.css({
                'height': localStorage.getItem('chat-height') + 'px'
            });

            $(document).on('mousedown','.chat_resize', function() {
                $(document).mousemove(function(e){
                    let y = e.pageY,
                        top = chat.offset().top,
                        li = 0;
                    $('.chat_content li').each(function(){
                        li+= $(this).outerHeight(true);
                    });
                    chat.css({
                        'height': y - top,
                        'max-height': li + 58
                    });
                    localStorage.setItem('chat-height', (y - top).toString());
                });
            });
            $(document).mouseup(function() {
                $(document).off('mousemove');
            });

            this.sockets.subscribe('forum.chat.msg', (msg) => {
                this.messages.push(msg);
                this.scrollDown();
            });

            this.sockets.subscribe('forum.chat.load', (msgs) => {
                this.messages = msgs;
                this.scrollDown();
            });

          this.sockets.subscribe('forum.chat.moder', (obj) => {
            this.isModer = obj.moder;
          });

          this.sockets.subscribe('forum.chat.delete', (text) => {
            this.messages = this.messages.filter(function (msg){
                return msg.text !== text;
            });
          });

            this.$socket.emit('forum.chat.load');
            this.$socket.emit('forum.chat.moder');
        },
        destroyed(){
            this.sockets.unsubscribe('forum.chat.msg');
            this.sockets.unsubscribe('forum.chat.load');
            this.sockets.unsubscribe('forum.chat.delete');
            this.sockets.unsubscribe('forum.chat.moder');
        },
        methods:{
            scrollDown(){
                this.$nextTick(function () {
                    var jchat = $('.chat_content');
                    jchat.scrollTop(jchat.prop('scrollHeight'));
                });
            },
            loadRecent(val){
                return val.split(',').map(id => parseInt(id));
            },
            send(){
                this.emoji = false;
                this.$socket.emit('forum.chat.msg', this.msg);
                this.msg = '';
            },
            deleteMsg(text){
                this.$socket.emit('forum.chat.delete', text);
            },
            parseMsg(text){
                text = text.replace(/<\/?[^>]+>/gi, '');
                for (let i = 0; i <= 58; i++){
                    text = text.replace(new RegExp(':emoji_' + i + ':', 'g'), '<i class="emoji emoji_' + i + '"></i>');
                }
                return text;
            },
            putEmoji(id, update = true){
                this.msg += ':emoji_' + id + ':';

                if (update){
                    this.recentEmoji = this.recentEmoji.filter(emoji => emoji !== id);
                    this.recentEmoji.unshift(id);

                    if (this.recentEmoji.length > 11){
                        this.recentEmoji = this.recentEmoji.slice(0, 11);
                    }
                    localStorage.setItem('recentEmoji', this.recentEmoji);
                }
            }
        }
    }
</script>

<style scoped>

</style>