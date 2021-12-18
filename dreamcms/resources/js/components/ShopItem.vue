<template>
    <a class="merchant_link">
        <div v-on:click="open" class="merchant_item">
            <div class="merchantborder"></div>
            <h3>{{ item.name }}</h3>
            <div class="merchant_item_middle">
                <div class="merchant_item_pic">
                    <img style="width: inherit;" :src="item.icon">
                </div> <!-- merchant_item_pic -->
                <div v-if="item.enchanted" class="merchant_item_enchanted">
                    <div class="merchant_enchanted_tag"></div>
                    <div class="merchant_enchanted_tip">Зачарован</div>
                </div> <!-- merchant_item_enchanted -->
            </div> <!-- merchant_item_middle -->
            <div class="merchant_item_buy_section">
                <div class="merchant_item_buy_section_cost">
                    <h3>{{ item.price }} руб</h3>
                    <span>ЗА {{ item.count }} ШТ.</span>
                </div> <!-- merchant_item_buy_section_cost -->
                <h3>КУПИТЬ</h3>
            </div> <!-- merchant_item_buy_section -->
        </div> <!-- merchant_item -->

        <modal :name="item.id.toString()" ref="buymodal" width="500px" height="350px" @before-open="beforeOpen">
            <div class="window_header">
                <h3>{{ item.name }}</h3>
                <div @click="$modal.hide(item.id.toString())" class="window_close"></div>
            </div> <!-- window_header -->
            <div class="window_content">
                <div class="window_buy_calc">
                    <div class="window_buy_calc_count">
                        <h3>{{ item.name }}</h3>
                        <span>за {{ item.count }} шт.</span>
                    </div> <!-- window_buy_calc_count -->
                    <div class="window_buy_calc_number">
                        <div class="minus" @click="addCount(-1)"><span></span></div>
                        <input v-model="count" id="slide-count" type="text" value="1" min="1" max="64"/>
                        <div class="plus" @click="addCount(1)"><span></span></div>
                    </div> <!-- window_buy_calc_number -->
                </div> <!-- window_buy_calc -->
                <div class="window_buy_pic">
                    <img style="width: inherit;" :src="item.icon" />
                </div>
                <template v-if="item.enchantable">
                <div class="open_window_enchants" @click="displayEnchants">
                    Зачаровать предмет
                </div>
                <div id="enchants" ref="enchants" style="display:none;">
                    <div class="enchants_container">
                        <table id="enchant-table" style="width: 100%">
                            <tbody>
                            <tr>
                                <td class="label"><abbr class="whelp" data-toggle="tooltip" title="" data-original-title="Преобразует весь урон от всех источников в урон брони">Защита</abbr></td>
                                <td class="range"><input type="range" class="enchInput" data-price="20" data-eid="0" value="0" min="0" max="10" step="1" v-model="enchants[getIndex(enchants, 0)].val"></td>
                                <td class="value" id="enchval0"><span style="color:#aaa;">{{ enchants[getIndex(enchants, 0)].val }}</span></td>
                            </tr>
                            <tr>
                                <td class="label"><abbr class="whelp" data-toggle="tooltip" title="" data-original-title="Защита от огня, лавы и огненных шаров ифритов. Уменьшает время горения">Огнестойкость</abbr></td>
                                <td class="range"><input type="range" class="enchInput" data-price="10" data-eid="1" value="0" min="0" max="10" step="1" v-model="enchants[getIndex(enchants, 1)].val"></td>
                                <td class="value" id="enchval1"><span style="color:#aaa;">{{ enchants[getIndex(enchants, 1)].val }}</span></td>
                            </tr>
                            <tr>
                                <td class="label"><abbr class="whelp" data-toggle="tooltip" title="" data-original-title="Защита от урона при падении">Лёгкость</abbr></td>
                                <td class="range"><input type="range" class="enchInput" data-price="10" data-eid="2" value="0" min="0" max="10" step="1" v-model="enchants[getIndex(enchants, 2)].val"></td>
                                <td class="value" id="enchval2"><span style="color:#aaa;">{{ enchants[getIndex(enchants, 2)].val }}</span></td>
                            </tr>
                            <tr>
                                <td class="label"><abbr class="whelp" data-toggle="tooltip" title="" data-original-title="Защита от взрывов. Уменьшает отдачу от взрывов">Взрывоустойчивость</abbr></td>
                                <td class="range"><input type="range" class="enchInput" data-price="10" data-eid="3" value="0" min="0" max="10" step="1" v-model="enchants[getIndex(enchants, 3)].val"></td>
                                <td class="value" id="enchval3"><span style="color:#aaa;">{{ enchants[getIndex(enchants, 3)].val }}</span></td>
                            </tr>
                            <tr>
                                <td class="label"><abbr class="whelp" data-toggle="tooltip" title="" data-original-title="Защита от снарядов (стрел и огненных шаров)">Защита от снарядов</abbr></td>
                                <td class="range"><input type="range" class="enchInput" data-price="10" data-eid="4" value="0" min="0" max="10" step="1" v-model="enchants[getIndex(enchants, 4)].val"></td>
                                <td class="value" id="enchval4"><span style="color:#aaa;">{{ enchants[getIndex(enchants, 4)].val }}</span></td>
                            </tr>
                            <tr>
                                <td class="label"><abbr class="whelp" data-toggle="tooltip" title="" data-original-title="Уменьшает потерю воздуха под водой, увеличивает время между приступами удушья">Дыхание</abbr></td>
                                <td class="range"><input type="range" class="enchInput" data-price="10" data-eid="5" value="0" min="0" max="10" step="1" v-model="enchants[getIndex(enchants, 5)].val"></td>
                                <td class="value" id="enchval5"><span style="color:#aaa;">{{ enchants[getIndex(enchants, 5)].val }}</span></td>
                            </tr>
                            <tr>
                                <td class="label"><abbr class="whelp" data-toggle="tooltip" title="" data-original-title="Увеличивает скорость работы под водой">Родство с водой</abbr></td>
                                <td class="range"><input type="range" class="enchInput" data-price="10" data-eid="6" value="0" min="0" max="1" step="1" v-model="enchants[getIndex(enchants, 6)].val"></td>
                                <td class="value" id="enchval6"><span style="color:#aaa;">{{ enchants[getIndex(enchants, 6)].val }}</span></td>
                            </tr>
                            <tr>
                                <td class="label"><abbr class="whelp" data-toggle="tooltip" title="" data-original-title="С некоторым шансом наносит урон атакующему">Шипы</abbr></td>
                                <td class="range"><input type="range" class="enchInput" data-price="15" data-eid="7" value="0" min="0" max="10" step="1" v-model="enchants[getIndex(enchants, 7)].val"></td>
                                <td class="value" id="enchval7"><span style="color:#aaa;">{{ enchants[getIndex(enchants, 7)].val }}</span></td>
                            </tr>
                            <tr>
                                <td class="label"><abbr class="whelp" data-toggle="tooltip" title="" data-original-title="Дополнительный урон">Острота</abbr></td>
                                <td class="range"><input type="range" class="enchInput" data-price="20" data-eid="16" value="0" min="0" max="10" step="1" v-model="enchants[getIndex(enchants, 16)].val"></td>
                                <td class="value" id="enchval16"><span style="color:#aaa;">{{ enchants[getIndex(enchants, 16)].val }}</span></td>
                            </tr>
                            <tr>
                                <td class="label"><abbr class="whelp" data-toggle="tooltip" title="" data-original-title="Дополнительный урон зомби, свинозомби, скелетам, иссушителям и скелетам-иссушителям">Небесная кара</abbr></td>
                                <td class="range"><input type="range" class="enchInput" data-price="10" data-eid="17" value="0" min="0" max="10" step="1" v-model="enchants[getIndex(enchants, 17)].val"></td>
                                <td class="value" id="enchval17"><span style="color:#aaa;">{{ enchants[getIndex(enchants, 17)].val }}</span></td>
                            </tr>
                            <tr>
                                <td class="label"><abbr class="whelp" data-toggle="tooltip" title="" data-original-title="Дополнительный урон паукам и чешуйницам">Бич членистоногих</abbr></td>
                                <td class="range"><input type="range" class="enchInput" data-price="10" data-eid="18" value="0" min="0" max="10" step="1" v-model="enchants[getIndex(enchants, 18)].val"></td>
                                <td class="value" id="enchval18"><span style="color:#aaa;">{{ enchants[getIndex(enchants, 18)].val }}</span></td>
                            </tr>
                            <tr>
                                <td class="label"><abbr class="whelp" data-toggle="tooltip" title="" data-original-title="Отбрасывает мобов и игроков">Отбрасывание</abbr></td>
                                <td class="range"><input type="range" class="enchInput" data-price="10" data-eid="19" value="0" min="0" max="10" step="1" v-model="enchants[getIndex(enchants, 19)].val"></td>
                                <td class="value" id="enchval19"><span style="color:#aaa;">{{ enchants[getIndex(enchants, 19)].val }}</span></td>
                            </tr>
                            <tr>
                                <td class="label"><abbr class="whelp" data-toggle="tooltip" title="" data-original-title="Поджигает цель">Аспект огня</abbr></td>
                                <td class="range"><input type="range" class="enchInput" data-price="10" data-eid="20" value="0" min="0" max="10" step="1" v-model="enchants[getIndex(enchants, 20)].val"></td>
                                <td class="value" id="enchval20"><span style="color:#aaa;">{{ enchants[getIndex(enchants, 20)].val }}</span></td>
                            </tr>
                            <tr>
                                <td class="label"><abbr class="whelp" data-toggle="tooltip" title="" data-original-title="Увеличивает добычу с мобов">Мародёрство</abbr></td>
                                <td class="range"><input type="range" class="enchInput" data-price="15" data-eid="21" value="0" min="0" max="10" step="1" v-model="enchants[getIndex(enchants, 21)].val"></td>
                                <td class="value" id="enchval21"><span style="color:#aaa;">{{ enchants[getIndex(enchants, 21)].val }}</span></td>
                            </tr>
                            <tr>
                                <td class="label"><abbr class="whelp" data-toggle="tooltip" title="" data-original-title="Более быстрая добыча ресурсов">Эффективность</abbr></td>
                                <td class="range"><input type="range" class="enchInput" data-price="25" data-eid="32" value="0" min="0" max="10" step="1" v-model="enchants[getIndex(enchants, 32)].val"></td>
                                <td class="value" id="enchval32"><span style="color:#aaa;">{{ enchants[getIndex(enchants, 32)].val }}</span></td>
                            </tr>
                            <tr>
                                <td class="label"><abbr class="whelp" data-toggle="tooltip" title="" data-original-title="При разрушении блока из него выпадает он сам">Шёлковое касание</abbr></td>
                                <td class="range"><input type="range" class="enchInput" data-price="20" data-eid="33" value="0" min="0" max="1" step="1" v-model="enchants[getIndex(enchants, 33)].val"></td>
                                <td class="value" id="enchval33"><span style="color:#aaa;">{{ enchants[getIndex(enchants, 33)].val }}</span></td>
                            </tr>
                            <tr>
                                <td class="label"><abbr class="whelp" data-toggle="tooltip" title="" data-original-title="С некоторым шансом ресурс инструмента не уменьшится">Прочность</abbr></td>
                                <td class="range"><input type="range" class="enchInput" data-price="30" data-eid="34" value="0" min="0" max="5" step="1" v-model="enchants[getIndex(enchants, 34)].val"></td>
                                <td class="value" id="enchval34"><span style="color:#aaa;">{{ enchants[getIndex(enchants, 34)].val }}</span></td>
                            </tr>
                            <tr>
                                <td class="label"><abbr class="whelp" data-toggle="tooltip" title="" data-original-title="Даёт шанс выпадения большего количества ресурсов">Удача</abbr></td>
                                <td class="range"><input type="range" class="enchInput" data-price="20" data-eid="35" value="0" min="0" max="5" step="1" v-model="enchants[getIndex(enchants, 35)].val"></td>
                                <td class="value" id="enchval35"><span style="color:#aaa;">{{ enchants[getIndex(enchants, 35)].val }}</span></td>
                            </tr>
                            <tr>
                                <td class="label"><abbr class="whelp" data-toggle="tooltip" title="" data-original-title="Дополнительный урон">Сила</abbr></td>
                                <td class="range"><input type="range" class="enchInput" data-price="10" data-eid="48" value="0" min="0" max="10" step="1" v-model="enchants[getIndex(enchants, 48)].val"></td>
                                <td class="value" id="enchval48"><span style="color:#aaa;">{{ enchants[getIndex(enchants, 48)].val }}</span></td>
                            </tr>
                            <tr>
                                <td class="label"><abbr class="whelp" data-toggle="tooltip" title="" data-original-title="Отбрасывание цели">Ударная волна</abbr></td>
                                <td class="range"><input type="range" class="enchInput" data-price="10" data-eid="49" value="0" min="0" max="10" step="1" v-model="enchants[getIndex(enchants, 49)].val"></td>
                                <td class="value" id="enchval49"><span style="color:#aaa;">{{ enchants[getIndex(enchants, 49)].val }}</span></td>
                            </tr>
                            <tr>
                                <td class="label"><abbr class="whelp" data-toggle="tooltip" title="" data-original-title="Поджигает стрелы">Воспламенение</abbr></td>
                                <td class="range"><input type="range" class="enchInput" data-price="10" data-eid="50" value="0" min="0" max="10" step="1" v-model="enchants[getIndex(enchants, 50)].val"></td>
                                <td class="value" id="enchval50"><span style="color:#aaa;">{{ enchants[getIndex(enchants, 50)].val }}</span></td>
                            </tr>
                            <tr>
                                <td class="label"><abbr class="whelp" data-toggle="tooltip" title="" data-original-title="Бесконечные стрелы">Бесконечность</abbr></td>
                                <td class="range"><input type="range" class="enchInput" data-price="15" data-eid="51" value="0" min="0" max="1" step="1" v-model="enchants[getIndex(enchants, 51)].val"></td>
                                <td class="value" id="enchval51"><span style="color:#aaa;">{{ enchants[getIndex(enchants, 51)].val }}</span></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <button class="close_window_enchants" @click="displayEnchants">
                        Сохранить изменения
                    </button>
                </div>
                </template>
                <div class="window_buy_totalcost">
                    <div>К оплате:</div>
                    <div>{{ price }} мон.</div>
                </div> <!-- window_buy_totalcost -->
                <button id="btn-item-buy" class="window_buy_button" @click="buy">КУПИТЬ</button>
            </div>
        </modal>
    </a>
</template>

<script>
    export default {
        props: ['item'],
        methods: {
            addCount(c){
                this.count = this.count + c;
            },

            displayEnchants(){
                $(this.$refs.enchants).toggle();
            },

            beforeOpen (event) {
                console.log(event.params);
            },
            open: function () {
                this.$modal.show(this.item.id.toString());
            },
            buy: function () {
                App.sendRequest('/shop/buy', {item_id: this.item.id, count: this.count, shop_id: App.shop.id, enchants: this.enchants});
            },
            getIndex (item, id) {
                return item.findIndex(i => i.id === id)
            }
        },
        data: function () {
            return {
                count: 0,
                price: 0,
                enchants: [
                    {id: 0, price: 20, val: 0},
                    {id: 1 , price: 10, val: 0},
                    {id: 2 , price: 10, val: 0},
                    {id: 3 , price: 10, val: 0},
                    {id: 4 , price: 10, val: 0},
                    {id: 5 , price: 10, val: 0},
                    {id: 6 , price: 10, val: 0},
                    {id: 7 , price: 15, val: 0},
                    {id: 16 , price: 20, val: 0},
                    {id: 17 , price: 10, val: 0},
                    {id: 18 , price: 10, val: 0},
                    {id: 19 , price: 10, val: 0},
                    {id: 20 , price: 10, val: 0},
                    {id: 21 , price: 15, val: 0},
                    {id: 32 , price: 25, val: 0},
                    {id: 33 , price: 20, val: 0},
                    {id: 34 , price: 30, val: 0},
                    {id: 35 , price: 20, val: 0},
                    {id: 48 , price: 10, val: 0},
                    {id: 49 , price: 10, val: 0},
                    {id: 50 , price: 10, val: 0},
                    {id: 51 , price: 15, val: 0}
                ]
            };
        },
        watch: {
            count (val){
                if (val <= 0) {
                    val = 1;
                    this.count = 1;
                }
                var ench_price = 0;

                this.enchants.forEach(ench => {
                    ench_price += ench.price * ench.val;
                });

                this.price = (val * (ench_price + (this.item.price / this.item.count))).toFixed(2);
            },
            enchants: {
                handler: function (val, oldVal) {
                    var ench_price = 0;

                    val.forEach(ench => {
                        ench_price += ench.price * ench.val;
                    });

                    this.price = (this.count * (ench_price + (this.item.price / this.item.count))).toFixed(2);

                },
                deep: true
            }
        }
    };
</script>
