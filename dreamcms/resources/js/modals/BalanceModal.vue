<template>
    <b-modal v-model="balanceModal" modal-class="modal" hide-header hide-footer content-class="custom_modal" size="lg">
        <div id="modal">
            <div class="window addBalance">
                <div class="header">
                    <h2>Пополнение баланса</h2>
                </div>
                <div class="px-3 px-sm-4 px-md-5 pt-3 pt-sm-4 pt-md-5 pb-3 pb-sm-4">
                    <div class="row align-items-center">
                        <p class="col-12 col-md mb-2 mb-md-0">Выберите нужную сумму для пополнения баланса</p>
                        <b-input type="number" class="col-12 col-md-4 form-control-lg" v-model="sum"></b-input>
                    </div>
                    <div class="bonus mt-2" v-if="sum > 200000">
                        А у Вас честно-честно есть ТАКИЕ деньжищи?:)
                    </div>
                    <div class="bonus mt-2" v-else>
                        После пополнения Вы получите <span id="mainStreams">{{ youGet }}</span> стримов<span id="bonusStreams"></span>.
                    </div>
                </div>
                <div class="footer py-4">
                    <user-selector v-if="forFriend" class="mb-4" v-model="selectedUser"></user-selector>

                    <a href="#" class="btn_large primary mb-2" @click.prevent="obmenka">
                        <span>Пополнить баланс {{ selectedUser ? 'для ' + selectedUser.login : '' }}</span>
                        <small>QIWI, банковские карты</small>
                    </a>

                    <a href="#" class="btn_large warning mb-2" @click.prevent="enot">
                        <span>Пополнить баланс {{ selectedUser ? 'для ' + selectedUser.login : 'через:' }}</span>
                        <small>ApplePay, карты не СНГ, ЮMoney, криптовалюта</small>
                    </a>

                    <a href="#" class="btn_large success mb-2" @click.prevent="digiseller">
                        <span>Пополнить баланс {{ selectedUser ? 'для ' + selectedUser.login : 'через:' }}</span>
                        <small>Мобильные операторы, WebMoney, QIWI, PayPal</small>
                    </a>

                    <a href="#" class="btn_large dark" @click.prevent="skinpay">
                        <small>Пополнить скинами через SkinPay {{ selectedUser ? ('для ' + selectedUser.login) : '' }}</small>
                    </a>

                    <a href="#" class="dashed_link mt-3 mt-sm-2" @click.prevent="forFriend = !forFriend">{{ forFriend ? 'Или пополните свой аккаунт' : 'Или переведите выбранную сумму своему другу' }}</a>
                </div>
            </div>
        </div>
    </b-modal>
</template>

<script>
    import api from "../api"
    import {mapGetters} from "vuex";
    import UserSelector from "../components/UserSelector";

    export default {
        name: "BalanceModal",
        components: {UserSelector},
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
            youGet(){
                if (this.sum >= 5000) return Math.round(this.sum * 1.30);
                if (this.sum >= 3000) return Math.round(this.sum * 1.25);
                if (this.sum >= 1000) return Math.round(this.sum * 1.15);
                if (this.sum >= 500) return Math.round(this.sum * 1.10);

                return Math.round(this.sum);
            }
        },
        data(){
            return {
                forFriend: false,
                selectedUser: null,
                sum: 100,
                payment: null
            }
        },
        methods:{
            isHovered(sum){
                if (sum === 500) return this.sum >= 500 && this.sum < 1000;
                if (sum === 1000) return this.sum >= 1000 && this.sum < 3000;
                if (sum === 3000) return this.sum >= 3000 && this.sum < 5000;
                if (sum === 5000) return this.sum >= 5000;
            },
            digiseller(){
              var data = {
                id_d: 3086435,
                typecurr: 'WMR',
                lang: 'ru-RU',
                failpage: 'https://' + window.location.hostname + '/page/failed',
                unit_cnt: Math.round(this.sum),
                uuid: this.selectedUser ? this.selectedUser.uuid : this.user.uuid
              }

              this.submit({method: 'POST', path: 'https://oplata.info/asp2/pay.asp', data: data});
            },
            /*unitpay(){
                this.payment = new UnitPay();
                this.payment.createWidget({
                    publicKey: "37321-6aa03",
                    sum: this.sum,
                    account: this.selectedUser ? this.selectedUser.uuid : this.user.uuid,
                    desc: "Пополнение баланса игрока " + (this.selectedUser ? this.selectedUser.login : this.user.login),
                    locale: "ru",
                });
                this.payment.success(function (params) {
                    console.log('Успешный платеж');
                });
                this.payment.error(function (message, params) {
                    console.log(message);
                });
            },*/
            skinpay(){
                api.post('pay/skinpay', {
                    account: this.selectedUser ? this.selectedUser.uuid : this.user.uuid,
                }).then(response => {
                    if (response.data.url){
                        window.location = response.data.url;
                    }
                });
            },
            obmenka(){
                api.post('pay/obmenka', {
                    account: this.selectedUser ? this.selectedUser.uuid : this.user.uuid,
                    sum: this.sum
                }).then(response => {
                    if (response.data.url){
                      window.location = response.data.url;
                    }
                });
            },
            enot(){
                api.post('pay/enot', {
                    account: this.selectedUser ? this.selectedUser.uuid : this.user.uuid,
                    sum: this.sum
                }).then(response => {
                    if (response.data.url){
                        window.location = response.data.url;
                    }
                });
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
        },
        watch:{
            forFriend: function(newVal){
                if (!newVal) this.selectedUser = null;
            },
            sum: function (newVal) {
                newVal = parseFloat(newVal);

                if (isNaN(newVal)) this.sum = 10;
                if (newVal < 10) this.sum = 10;
                if (newVal > 1000000) this.sum = 1000000;
            }
        }
    }
</script>
