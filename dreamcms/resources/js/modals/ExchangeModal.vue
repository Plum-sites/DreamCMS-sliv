<template>
    <b-modal v-model="exchangeModal" modal-class="modal" hide-header hide-footer content-class="custom_modal" size="lg">
        <div id="modal" :class="loading ? 'unload' : ''">
            <div class="window exchangeCoins">
                <div class="header px-3 px-sm-5 pb-2 pb-sm-4">
                    <h2>Обменный пункт</h2>
                    <div class="row justify-content-center">
                        <p class="col-9 p-0">Коины — это валюта для торговли с игроками на наших игровых серверах, 1 стрим = 100 коинов. Пополняйте баланс и становитесь круче на любимом сервере!:)</p>
                    </div>
                </div>
                <div class="row px-3 px-sm-5 pb-4 pb-md-5 justify-content-center justify-content-md-start">
                    <div class="col-12 col-sm-9 col-md-7 col-lg-6">
                        <input type="number" class="form-control" placeholder="Количество стримов" min="0" v-model="count">

                        <v-select class="btn_common select mt-2" label="name" :filterable="false" :options="Object.values(this.servers).filter(server => server.ecomanager)" placeholder="Выберите сервер" v-model="selectedServer"></v-select>

                        <a href="#" :class="'btn_large success font-weight-bold mt-3 ' + (selectedServer && count > 0 ? '' : 'disabled')" @click.prevent="exchange">
                            <span>Перевести коины</span>
                            <small>Вы получите {{ Math.round(count * 100) }} коинов {{ selectedServer ? 'на сервере ' + selectedServer.name : '' }}</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </b-modal>
</template>

<script>
    import api from "../api"
    import {mapGetters} from "vuex";

    export default {
        name: "ExchangeModal",
        computed:{
            ...mapGetters(['user', 'servers']),
            exchangeModal: {
                get () {
                    return this.$store.getters.exchangeModal;
                },
                set (newValue) {
                    return this.$store.dispatch('setExchangeModal', newValue);
                }
            },
        },
        data(){
            return {
                loading: false,

                selectedServer: null,
                count: 10
            }
        },
        methods:{
            exchange(){
                this.loading = true;

                api.post('profile/economy/send', {
                    server: this.selectedServer.id,
                    count: this.count,
                }).then(response => {
                    this.loading = false;
                }).catch(error => {
                    console.log(error);
                });
            }
        }
    }
</script>
