<template>
    <loader v-if="this.loading"></loader>
    <div v-else>
        <div class="row">
            <div class="col-6 mx-auto">
                <b-input type="number" class="col-12 form-control-lg mb-5" v-model="sum" placeholder="Введите сумму"></b-input>
            </div>
        </div>
        <div class="row">
            <div class="col-6 mx-auto">
                <a href="#" class="btn_large primary mb-2" @click.prevent="obmenka">
                    <span>Пополнить баланс</span>
                    <small>QIWI, банковские карты</small>
                </a>

                <a href="#" class="btn_large warning mb-2" @click.prevent="enot">
                    <span>Пополнить баланс</span>
                    <small>ApplePay, карты не СНГ, ЮMoney, криптовалюта</small>
                </a>

                <a href="#" class="btn_large success mb-2" @click.prevent="digiseller">
                    <span>Пополнить баланс</span>
                    <small>Мобильные операторы, WebMoney, QIWI, PayPal</small>
                </a>

                <a href="#" class="btn_large dark" @click.prevent="skinpay">
                    <small>Пополнить скинами через SkinPay</small>
                </a>
            </div>
        </div>
    </div>
</template>

<script>
    import api from '../api'
    import Loader from "../components/Loader";

    export default {
        name: "Pay",
        components: {Loader},
        mounted() {
            this.load();
        },
        data() {
            return {
                loading: true,
                user: {},
                sum: null
            }
        },
        methods: {
            load() {
                this.loading = true;

                if (this.$route.query.username){
                    api.get('core/user/find?login=' + this.$route.query.username).then(response => {
                        if (response.data.success){
                            this.user = response.data.user;
                            this.loading = false;
                        }
                    });
                }

                if (this.$route.query.uuid){
                    this.user = {
                        uuid: this.$route.query.uuid
                    };
                    this.loading = false;
                }

                if (this.$route.query.sum){
                    this.sum = parseInt(this.$route.query.sum);
                }
            },
            skinpay(){
                api.post('pay/skinpay', {
                    account: this.user.uuid,
                }).then(response => {
                    if (response.data.url){
                        window.location = response.data.url;
                    }
                });
            },
            obmenka(){
                api.post('pay/obmenka', {
                    account: this.user.uuid,
                    sum: this.sum
                }).then(response => {
                    if (response.data.url){
                        window.location = response.data.url;
                    }
                });
            },
            enot(){
                api.post('pay/enot', {
                    account: this.user.uuid,
                    sum: this.sum
                }).then(response => {
                    if (response.data.url){
                        window.location = response.data.url;
                    }
                });
            },
            digiseller(){
                var data = {
                    id_d: 3086435,
                    typecurr: 'WMR',
                    lang: 'ru-RU',
                    failpage: 'https://' + window.location.hostname + '/page/failed',
                    unit_cnt: Math.round(this.sum),
                    uuid: this.user.uuid
                }

                this.submit({method: 'POST', path: 'https://oplata.info/asp2/pay.asp', data: data});
            },
            makeElement(tagName, attributes, ...children) {
                let element = document.createElement(tagName);
                Object.assign(element, attributes);

                for (let child of children) {
                    element.appendChild(child);
                }

                return element;
            },
            submit({ method = 'POST', path, data }) {
                const form = this.makeElement(
                    'form', { action: path, method },
                    ...Object.entries(data).map(([name, value]) => this.makeElement('input', { name, value }))
                );

                document.body.appendChild(form)

                form.submit()
            }
        }
    }
</script>

<style scoped>

</style>