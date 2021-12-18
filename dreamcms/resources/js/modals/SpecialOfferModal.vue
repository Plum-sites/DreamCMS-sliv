<template>
    <b-modal v-model="open" modal-class="modal" hide-header centered hide-footer content-class="custom_modal" size="xl">
        <div id="modal" v-if="current_offer">
            <div class="window promo">
                <div class="running">
                    <i></i> <i></i> <i></i> <i></i>
                    <i></i> <i></i> <i></i> <i></i>
                    <i></i> <i></i> <i></i> <i></i>
                </div>
                <div class="row px-4 m-0">
                    <div class="col-12 col-md-8 px-0 pt-2">
                        <div class="about py-3 px-md-3">
                            <span v-html="current_offer.description"></span>
                            <ul class="mt-3">
                                <li class="font-weight-bold">Предложение доступно только до {{ formatUnix(current_offer.expire) }}:</li>
                                <li v-for="offer in offers"><span v-html="offer.discount_desc"></span></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col p-0 d-none d-md-block"></div>
                    <div class="col-12 p-0 px-md-3 pb-4 mt-md-2">
                        <a href="#" @click.prevent="goToRoute('donate')" class="btn_large primary">
                            <span>Перейти к покупкам</span>
                        </a>
                        <a href="#" class="btn_large dark ml-0 mt-2 mt-md-0 ml-md-3" @click="closeForDay">
                            <span>не сейчас</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </b-modal>
</template>

<script>
import api from '../api';
import {mapGetters} from "vuex";

export default {
    name: "SpecialOfferModal",
    data(){
        return {
            offers: [],
            current_offer: null,
            open: false
        }
    },
    computed:{
        ...mapGetters(['isLogged'])
    },
    async created(){
        if (localStorage.getItem('offermodal_close') && this.nowUnix() - localStorage.getItem('offermodal_close') < 3600 * 3) return;

        api.get('core/offers').then(response =>{
            if (response.data.success){
                this.offers = response.data.offers;

                if (this.offers.length > 0){
                    this.current_offer = this.offers[0];
                    this.open = true;
                }
            }
        });
    },
    methods:{
        goToRoute(name){
            this.open = false;
            this.$router.push({name: name});
        },
        closeForDay(){
            localStorage.setItem('offermodal_close', this.nowUnix());
            this.open = false;
        }
    }
}
</script>
