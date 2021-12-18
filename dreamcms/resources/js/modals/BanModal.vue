<template>
    <b-modal v-model="open" modal-class="modal" hide-header hide-footer content-class="custom_modal" size="lg">
        <div id="modal">
            <div class="window ban" v-if="bans.length > 0">
                <div class="header">
                    <h2>Вы заблокированы на наших серверах</h2>
                    <p>
                        <router-link :to="{name: 'banlist'}">Перейти в банлист</router-link> или
                        <a href="#" @click.prevent="closeForDay">закрыть на 3 часа</a>
                    </p>
                    <a href="#" class="btn_large dark mt-4" @click.prevent="buyUnban" v-if="bans[0].price">
                        <span>Платный разбан за {{ bans[0].price }} руб</span>
                    </a>
                    <a href="https://vk.com/vk" class="btn_large dark mt-4" v-else>
                        <span>Платный разбан</span>
                    </a>
                </div>
            </div>
        </div>
    </b-modal>
</template>

<script>
    import api from '../api';
    import {mapGetters} from "vuex";

    export default {
        name: "BanModal",
        computed:{
            ...mapGetters(['bans'])
        },
        data(){
            return {
                open: false
            }
        },
        mounted() {
            if (this.bans.length > 0){
                if (localStorage.getItem('banmodal_close') && this.nowUnix() - localStorage.getItem('banmodal_close') < 3600 * 3) return;
                this.open = true;
            }
        },
        methods:{
            buyUnban(){
                api.post('profile/unban/buy');
            },
            closeForDay(){
                localStorage.setItem('banmodal_close', this.nowUnix());
                this.open = false;
            }
        }
    }
</script>