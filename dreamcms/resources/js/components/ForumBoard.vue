<template>
    <div class="board">
        <div class="row m-0 text-center text-md-left justify-content-center justify-content-md-start">
            <span class="col-12 col-xl p-0">Пользователей в сети — {{ online.length }}</span>
            <router-link :to="{name: 'forum_team'}">Администрация</router-link>
        </div>
        <ul class="text-center text-md-left">
            <li v-for="user in online">
                <router-link :to="{name: 'user', params: {login: user.login}}">
                    <span :style="'color: ' + getColor(user.role) + ';'">{{ user.login }}</span>
                </router-link>
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        name: "ForumBoard",
        data(){
            return {
                online: []
            }
        },
        mounted() {
            this.sockets.subscribe('forum.online', (data) => {
                this.online = data;
            });
            this.$socket.emit('forum.online');
        },
        methods:{
            getColor(html){
                try {
                    var htmlDoc = new DOMParser().parseFromString(html, 'text/html');
                    return htmlDoc.getElementsByTagName('span')[0].style.color;
                }catch (e) {
                    return 'inherit';
                }
            }
        }
    }
</script>

<style scoped>

</style>