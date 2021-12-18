<template>
    <div :class="'inner ' + (this.loading ? 'unload' : '')" v-if="!this.$route.params.shop">
        <div class="headline">
            <h2>Магазин блоков</h2>
            <p>Рады Вас приветствовать в новом полностью переработанном онлайн-магазине! Для начала выберите нужный сервер и приступайте к покупкам!</p>
        </div>
        <div class="shop">
            <div class="row servers justify-content-center">
                <div class="col-12 col-md-6 col-lg-12 col-xl-6" v-for="server in shops">
                    <router-link :to="{name: 'shop', params: {shop: server.name}}" class="category">
                        <div class="col">
                            <h3>{{ server.name }} <b v-if="server.discount" class="float-right">-{{ server.discount }}%</b></h3>
                            <p>Всего <b>{{ server.items_count }}</b> {{ declOfNum(server.categories.length, ['предмет', 'предмета', 'предметов']) }}</p>
                            <p>из <b>{{ server.categories.length }}</b> {{ declOfNum(server.categories.length, ['категории', 'категорий', 'категорий']) }}.</p>
                        </div>
                        <img :src="server.icon" alt>
                    </router-link>
                </div>
            </div>
        </div>
    </div>
    <div :class="'inner ' + (this.loading ? 'unload' : '')" v-else>
        <div class="headline">
            <h2>Магазин блоков</h2>
            <p>Рады Вас приветствовать в новом полностью переработанном онлайн-магазине! Для начала выберите нужный сервер и приступайте к покупкам!</p>
        </div>
        <div class="shop">
            <div class="section primary search">
                <div class="row align-items-center">
                    <i class="fal fa-search d-none d-sm-block"></i>
                    <input class="col text-center text-sm-left pl-0" placeholder="Введите текст для поиска..." minlength="1" maxlength="16" v-model="search.text">
                </div>
                <div class="row align-items-center" v-if="shop">
                    <div class="col-12 col-md-6 col-lg-12 col-xl-6 px-0">
                        <v-select class="btn_common btn_common_lg select" :options="shop.categories" label="name" :reduce="category => category.id" v-model="search.category" placeholder="Выберите категорию">
                            <template v-slot:option="category">
                                <img :src="category.icon">
                                {{ category.name }}
                                <span v-if="category.discount">(- {{category.discount}}%)</span>
                            </template>
                        </v-select>
                    </div>
                    <div class="col-12 col-md-6 col-lg-12 col-xl-6 mt-2 mt-md-0 mt-lg-2 mt-xl-0 pl-0 pl-md-3 pl-lg-0 pl-xl-3 pr-0">
                        <v-select class="btn_common btn_common_lg select" :options="[
                            {id: 1, name: 'По порядку'},
                            {id: 2, name: 'По убыванию цены'},
                            {id: 3, name: 'По возрастанию цены'},
                            {id: 4, name: 'Сначала со скидкой'},
                        ]" label="name" :reduce="sort => sort.id" v-model="search.sort">
                        </v-select>
                    </div>
                </div>
            </div>
            <div class="row items justify-content-center mt-4">
                <a href="#" class="col btn_common" v-for="item in items" @click.prevent="openItem(item)">
                    <h4>{{ item.name }}</h4>
                    <img :src="item.icon" width="64" height="64">
                    <span class="btn_common success" v-if="item.discount">
                        {{ item.price }} {{ declOfNum(item.price, ['стрим', 'стрима', 'стримов']) }}
                        <small>(-{{item.discount}}%)</small>
                        <small>за {{ item.count }} шт.</small>
                    </span>
                    <span class="btn_common primary" v-else>
                        {{ item.price }} {{ declOfNum(item.price, ['стрим', 'стрима', 'стримов']) }}
                        <small>за {{ item.count }} шт.</small>
                    </span>
                </a>
            </div>
            <div class="text-center mt-3" v-if="current_page < last_page">
                <a href="#" class="btn_large primary d-inline-block" @click.prevent="loadMore">
                    <span>Загрузить ещё товары</span>
                </a>
            </div>
        </div>

        <b-modal v-model="modal" modal-class="modal" hide-header hide-footer content-class="custom_modal" size="lg" v-if="item">
            <div id="modal" :class="this.buy.loading ? 'unload' : ''">
                <div class="window buyItem">
                    <div class="header">
                        <h2>{{ item.name }}</h2>
                        <p>Магазин: <b>{{ shop.name }}</b></p>
                    </div>
                    <div class="body">
                        <div class="row align-items-center justify-content-center">
                            <img :src="item.icon" width="140" height="140">
                            <div class="col-12 col-sm pl-4">
                                <ul class="meta">
                                    <li>
                                        <span>ID предмета:</span>
                                        <b>{{ item.type + (item.damage ? (':' + item.damage) : '') }}</b>
                                    </li>
                                    <li v-if="!item.enchantable">
                                        <span>Зачарование:</span>
                                        <b class="danger">Невозможно</b>
                                    </li>
                                    <li class="strike" v-if="item.oldprice">
                                        <span>Старая цена:</span>
                                        <b>{{ item.oldprice }} {{ declOfNum(item.oldprice, ['стрим', 'стрима', 'стримов'])  }} за {{ item.count }} шт.</b>
                                    </li>
                                    <li>
                                        <span>Цена:</span>
                                        <b>{{ item.price }} {{ declOfNum(item.price, ['стрим', 'стрима', 'стримов']) }} за {{ item.count }} шт.</b>
                                    </li>
                                    <li v-if="item.discount">
                                        <span>Скидка:</span>
                                        <b class="primary">Цена снижена на {{ item.discount }}%!</b>
                                    </li>
                                    <li>
                                        <span class="col-6 px-0">Количество:</span>
                                        <input type="number" class="form-control ml-auto" placeholder="Кол-во" v-model="buy.count" min="1">
                                    </li>
                                    <li class="text-center text-sm-left d-block" v-if="item.enchantable">
                                        <a href="#" class="dashed_link" onclick="$('.enchant').fadeIn('150')">Наложить зачарования на предмет</a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="footer">
                        <a href="#" class="btn_large success" @click.prevent="buyItem">
                            <span>Купить за <span id="price">{{ countPrice() }}</span> {{ declOfNum(countPrice(), ['стрим', 'стрима', 'стримов']) }}</span>
                        </a>
                    </div>
                    <div class="enchant" style="display: none;">
                        <div class="content">
                            <h3>
                                Зачаровать предмет
                                <a href="#" onclick="$('.enchant').fadeOut('150')">X</a>
                            </h3>
                            <ul>
                                <li :class="getEnchantLvl(enchant) ? 'checked' : ''" v-for="enchant in all_enchants" @click.left="changeEnchant(enchant, true)" @click.right="changeEnchant(enchant, false)">
                                    <div>{{ enchant.name }} <span>{{ getEnchantLvl(enchant) + ' / ' + enchant.max_level}}</span></div>
                                    <div class="col"></div>
                                    <div>{{ getEnchantPrice(enchant) }} стрим.</div>
                                </li>
                            </ul>
                            <p>
                                <a href="#" class="enchant_save" onclick="$('.enchant').fadeOut('150')">Сохранить изменения</a>
                            </p>
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
        name: "Shop",
        data(){
            return {
                loading: true,

                modal: false,

                shops: [],
                shop: null,

                search:{
                    text: '',
                    category: 0,
                    sort: 1
                },

                items: [],

                item: null,

                buy: {
                    loading: false,
                    count: 1
                },

                all_enchants: [],
                current_enchants: {},
                cacheIndex: 0,

                current_page: 1,
                last_page: 1
            }
        },
        mounted(){
            if (this.$route.params.shop){
                this.loadShop();
            } this.loadShops();
        },
        methods: {
            countPrice(){
                var ench_price = 0;
                var _this = this;
                this.cacheIndex;
                this.all_enchants.forEach(function (enchant) {
                    if (_this.current_enchants[enchant.id]){
                        ench_price += enchant.price * _this.current_enchants[enchant.id];
                    }
                });
                return Math.ceil(this.buy.count * (ench_price + (this.item.price / this.item.count)));
            },
            getEnchantLvl(enchant){
                this.cacheIndex;
                var lvl = this.current_enchants[enchant.id];
                if (lvl) return lvl;
                return 0;
            },
            changeEnchant(enchant, incr){
                console.log('change ' + enchant.name + ' ' + incr);
                var lvl = this.current_enchants[enchant.id];
                if (lvl){
                    if (incr){
                        if (lvl < enchant.max_level){
                            this.current_enchants[enchant.id] = lvl + 1;
                        }else {
                            this.current_enchants[enchant.id] = 0;
                        }
                    }else {
                        if (lvl >= 1){
                            this.current_enchants[enchant.id] = lvl - 1;
                        }
                    }
                }else if(incr){
                    this.current_enchants[enchant.id] = 1;
                }
                this.cacheIndex++;
            },
            getEnchantPrice(enchant){
                this.cacheIndex;
                var lvl = this.current_enchants[enchant.id];
                if (lvl) {
                    return Math.round(enchant.price * lvl);
                }
                return 0;
            },
            buyItem(){
                this.buy.loading = true;

                api.post('shop/buy', {
                    shop_id: this.shop.id,
                    item_id: this.item.id,
                    count: this.buy.count,
                    enchants: this.current_enchants
                })
                .finally(() => {
                    this.buy.loading = false;
                });
            },
            openItem(item){
                this.current_enchants = {};
                this.item = item;
                this.modal = true;
            },
            loadShops(){
                this.loading = true;

                api.get('shop')
                .then(response => {
                    if (response.data.success){
                        this.shops = response.data.shops;
                    }

                    this.loading = false;
                });
            },
            loadShop(){
                this.loading = true;

                api.post('shop/load', {
                    shop: this.$route.params.shop,
                    category: this.search.category,
                    search: this.search.text,
                    sort: this.search.sort,
                })
                .then(response => {
                    if (response.data.success){
                        this.shop = response.data.shop;

                        this.shop.categories.unshift({
                            id: 0,
                            discount: 0,
                            name: 'Все категории'
                        });

                        this.items = response.data.items.data;
                        this.all_enchants = response.data.enchants;
                        this.current_page = response.data.items.current_page;
                        this.last_page = response.data.items.last_page;
                        this.loading = false;
                    }
                });
            },
            loadMore(){
                this.loading = true;
                this.current_page++;

                api.post('shop/load', {
                    shop: this.$route.params.shop,
                    category: this.search.category,
                    search: this.search.text,
                    sort: this.search.sort,
                    page: this.current_page
                })
                    .then(response => {
                        if (response.data.success){
                            this.items = this.items.concat(response.data.items.data)
                            this.current_page = response.data.items.current_page;

                            this.loading = false;
                        }
                    });
            }
        },
        watch:{
            '$route': function (to) {
                if (to.params.shop){
                    this.search = { text: '', category: 0, sort: 1};

                    this.loadShop();
                }else{
                    this.loadShops();
                }
            },
            'search.text': function (newVal) {
                if (newVal && newVal.length >= 3){
                    this.loadShop()
                }
            },
            'search.category': function () {
                this.loadShop()
            },
            'search.sort': function () {
                this.loadShop()
            }
        }
    }
</script>
