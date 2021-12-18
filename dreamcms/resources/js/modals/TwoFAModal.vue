<template>
    <b-modal v-model="open" modal-class="modal" hide-header hide-footer content-class="custom_modal" size="lg">
        <div id="modal">
            <div class="window ban">
                <div class="header">
                    <h2>Уважаемый игрок!</h2>
                    <p>
                        Как мы видим, вы приобрели донат, но не включили двухфакторную авторизацию. Она сильно повышает безопасность аккаунта и мы настоятельно рекомендуем <router-link :to="{name: 'security'}" @click.native="open = false">подключить</router-link> ее.
                    </p>
                    <p>
                        <a href="#" @click.prevent="closeForDay">Закрыть на 3 часа</a>
                    </p>
                </div>
            </div>
        </div>
    </b-modal>
</template>

<script>
    import api from '../api';
    import {mapGetters} from "vuex";
    import moment from 'moment';

    export default {
        name: "TwoFAModal",
        computed:{
            ...mapGetters(['dgroups', 'otp'])
        },
        data(){
            return {
                open: false
            }
        },
        mounted() {
            if (this.dgroups.length > 0 && !this.otp){
                if (localStorage.getItem('twofamodal_close') && this.nowUnix() - localStorage.getItem('twofamodal_close') < 3600 * 3) return;
                this.open = true;
            }
        },
        methods: {
            closeForDay(){
                localStorage.setItem('twofamodal_close', this.nowUnix());
                this.open = false;
            }
        }
    }
</script>