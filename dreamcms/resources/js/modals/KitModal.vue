<template>
    <b-modal modal-class="modal" hide-header hide-footer content-class="custom_modal" size="lg">
        <div id="modal">
            <div class="window aboutKits">
                <div class="header mb-2">
                    <a href="#" class="d-block d-sm-none mt-2" @click.prevent="balanceModal = false">Закрыть <i class="far fa-window-close"></i></a>
                    <h2>Набор ресурсов «VIP»</h2>
                    <div class="row justify-content-center">
                        <p class="col-12 col-md-9 p-0">Все комплекты с игровыми предметами разграничены, для каждого сервера предусмотрен собственный уникальный состав вещей, обратите на это внимание!</p>
                        <p class="col-12 mt-3"><a href="#" class="btn_common primary">Купить за 80 рублей</a></p>
                    </div>
                </div>
                <div class="body">
                    <div class="row justify-content-center">
                        <div class="col-12 col-sm-6">
                            <h3>SandBox</h3>
                            <img src="img/any/Sandbox-vip.png">
                        </div>
                        <div class="col-12 col-sm-6">
                            <h3>TechnoMagic</h3>
                            <img src="img/any/TechnoMagic-vip.png">
                        </div>
                        <div class="col-12 col-sm-6">
                            <h3>NanoTech</h3>
                            <img src="img/any/Nanotech-vip.png">
                        </div>
                        <div class="col-12 col-sm-6">
                            <h3>Galactic</h3>
                            <img src="img/any/Galactic-vip.png">
                        </div>
                        <div class="col-12 col-sm-6">
                            <h3>Magic</h3>
                            <img src="img/any/Magic-vip.png">
                        </div>
                        <div class="col-12 col-sm-6">
                            <h3>RPG</h3>
                            <img src="img/any/Rpg-vip.png">
                        </div>
                        <div class="col-12 col-sm-6">
                            <h3>TechnoMagicSky</h3>
                            <img src="img/any/TechnoMagicSky-vip.png">
                        </div>
                        <div class="col-12 col-sm-6">
                            <h3>Pixelmon</h3>
                            <img src="img/any/Pixelmon-vip.png">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </b-modal>
</template>

<script>
    import {mapGetters} from "vuex";

    export default {
        name: "KitModal",
        props: ['kit'],
        computed:{
            ...mapGetters(['user']),
            balanceModal: {
                get () {
                    return this.$store.getters.balanceModal;
                },
                set (newValue) {
                    return this.$store.dispatch('setBalanceModal', newValue);
                }
            },
        },
        data(){
            return {
                //kit: null,
            }
        },
    }
</script>
