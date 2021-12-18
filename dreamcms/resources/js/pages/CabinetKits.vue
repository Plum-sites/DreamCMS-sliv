<template>
    <div :class="this.loading ? 'unload' : ''">
        <div class="section kits">
            <div class="row">
                <div class="col-12 col-lg-7 col-xl-6">
                    <h3>Эпические наборы ресурсов!</h3>
                    <p>Хотите стать крутым архитектором или храбрым искателем приключений? Приобретайте отдельные наборы с самыми эпическими ресурсами и воплощайте все свои мечты!</p>
                    <p>Коллекция наборов будет расширяться, давая Вам ещё более интересные и выгодные предложения, с которыми любимая игра заиграет совершенно новыми красками!</p>
                </div>
            </div>
        </div>
        <div class="row resource_kits justify-content-center">
            <div class="col-12 col-sm-6 col-md-4 col-lg mb-3" v-for="kit in kits">
                <a href="#" :class="kit.name" @click.prevent="openKit(kit)">
                    <h5>
                        <small>Набор ресурсов</small>
                        {{ kit.name }}
                    </h5>
                    <p>Кликните, чтобы увидеть больше подробностей</p>
                </a>
            </div>

            <div class="col-12 col-sm-6 col-md-4 col-lg d-block d-sm-none d-md-block d-lg-none d-xl-block mb-3" v-if="!this.loading">
                <a href="#" class="next" @click.prevent="">
                    <h5><small>Грядущие новинки!</small></h5>
                    <p class="whats_next">Постепенно мы будем пополнять коллекцию всё новыми и новыми наборами, не скучайте!</p>
                </a>
            </div>
        </div>

        <b-modal modal-class="modal" hide-header hide-footer content-class="custom_modal" size="lg" v-for="kit in kits" :key="kit.name" :id="kit.name">
            <div id="modal" :class="loading ? 'unload' : ''">
                <div class="window aboutKits">
                    <div class="header mb-2">
                        <a href="#" class="d-block d-sm-none mb-2" @click.prevent="hideCurrent">Закрыть <i class="far fa-window-close"></i></a>
                        <h2>Набор ресурсов «{{ kit.name.toUpperCase() }}»</h2>
                        <div class="row justify-content-center">
                            <p class="col-12 col-md-9 p-0">Все комплекты с игровыми предметами разграничены, для каждого сервера предусмотрен собственный уникальный состав вещей, обратите на это внимание!</p>
                            <p class="col-12 mt-3">
                                <a href="#" class="btn_common primary" @click.prevent="buyKit(kit)">Купить за {{ kit.price }} стримов</a>
                            </p>
                        </div>
                    </div>
                    <div class="body">
                        <div class="row justify-content-center">
                            <div class="col-12 col-sm-6" v-for="(src, server) in kit.images">
                                <h3>{{ server }}</h3>
                                <img :src="src" alt>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </b-modal>
    </div>
</template>

<script>
    import api from '../api';

    export default {
        name: "CabinetKits",
        data(){
            return {
                loading: true,
                kits: [],
                current_kit: null
            }
        },
        async mounted(){
            this.loadKits();
        },
        methods:{
            openKit(kit){
                this.current_kit = kit.name;
                this.$bvModal.show(kit.name);
            },
            hideCurrent(){
                this.$bvModal.hide(this.current_kit);
            },
            buyKit(kit){
                this.loading = true;

                api.post('profile/kits/buy',{
                    kit: kit.name
                })
                .then(response => {
                    this.loading = false;
                });
            },
            loadKits(){
                this.loading = true;

                api.get('profile/kits')
                .then(response => {
                    this.kits = response.data.kits;
                    this.loading = false;
                });
            }
        }
    }
</script>

<style scoped>

</style>