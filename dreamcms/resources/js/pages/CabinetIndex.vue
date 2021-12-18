<template>
    <div class="row character">
        <div :class="'col-12 col-lg-6 ' + (skinLoading ? 'unload' : '')">
            <div class="section">
                <h3>Внешний вид персонажа</h3>
                <p>Хотите подчеркнуть свою индивидуальность и выглядеть по-настоящему круто? Персонализируйте Вашего игрового персонажа и загрузите скин всего в два клика!</p>
            </div>
            <div class="section preview">
                <div class="row">
                    <div class="col-12 col-sm viewer">
                        <div class="viewer_dim" v-if="!flatSkin">
                            <div id="skin_container"></div>
                        </div>
                        <div class="viewer_dim mt-4" v-if="flatSkin">
                            <img :src="'/skin/' + user.uuid + '/front/260'" width="128" height="256">
                            <img :src="'/skin/' + user.uuid + '/back/260'" width="128" height="256">
                        </div>
                        <div class="control_view">
                            <a href="#" class="btn_common" @click.prevent="rotate.paused = !rotate.paused">
                                <i class="fas fa-sync-alt"></i>
                            </a>
                            <a href="#" class="btn_common" @click.prevent="skinViewer.animationPaused = !skinViewer.animationPaused">
                                <i class="fas fa-pause"></i>
                            </a>
                            <a href="#" class="btn_common" @click.prevent="flatSkin = !flatSkin">
                                {{ flatSkin ? '3D' : '2D' }}
                            </a>
                        </div>
                    </div>
                    <div class="col-12 col-sm mt-4 mt-sm-0">
                        <div>
                            <h4>Скин</h4>
                            <p v-if="hasPermission('upload.skin.hd.edit')">Вы можете загрузить любой скин в высоком HD качестве!</p>
                            <p v-else>Всем игрокам доступна загрузка скинов в классическом 64x64 разрешении.</p>
                            <p class="mt-2">
                                <a href="" class="btn_common" @click.prevent="uploadSkin">Загрузить</a>
                                <a href="#" target="_blank" class="btn_common ml-1" @click.prevent="downloadFile('/skins/' + user.uuid + '.png', user.login + '_skin.png')">
                                    <i class="fas fa-arrow-alt-to-bottom"></i>
                                </a>
                                <a href="" class="btn_common ml-1" v-if="hasPermission('upload.skin.delete')" @click.prevent="deleteSkin">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </p>
                        </div>
                        <div class="mt-5">
                            <h4>Плащ</h4>

                            <div v-if="hasPermission('upload.cloak.hd.edit')">
                                <p>Загрузите плащ в высоком качестве HD разрешения прямо сейчас!</p>
                                <p class="mt-2">
                                    <a href="" class="btn_common" @click.prevent="uploadCloak">Загрузить</a>
                                    <a href="#" target="_blank" class="btn_common ml-1" @click.prevent="downloadFile('/cloaks/' + user.uuid + '.png', user.login + '_cloak.png')">
                                        <i class="fas fa-arrow-alt-to-bottom"></i>
                                    </a>
                                    <a href="" class="btn_common ml-1" v-if="hasPermission('upload.cloak.delete')" @click.prevent="deleteCloak">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </p>
                            </div>
                            <div v-else>
                                <div v-if="hasPermission('upload.cloak.edit')">
                                    <p>Надевается на спину Вашего персонажа на всех наших игровых серверах.</p>
                                    <p class="mt-2">
                                        <a href="" class="btn_common" @click.prevent="uploadCloak">Загрузить</a>
                                        <a href="#" target="_blank" class="btn_common ml-1" @click.prevent="downloadFile('/cloaks/' + user.uuid + '.png', user.login + '_cloak.png')">
                                            <i class="fas fa-arrow-alt-to-bottom"></i>
                                        </a>
                                        <a href="" class="btn_common ml-1" v-if="hasPermission('upload.cloak.delete')" @click.prevent="deleteCloak">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </p>
                                </div>
                                <div v-else>
                                    <p>Загрузка плаща доступна только для группы VIP и выше.</p>
                                    <p class="mt-2">
                                        <a href="#" class="btn_common mr-1" @click.prevent="deleteCloak"><i class="fas fa-trash-alt"></i></a>
                                        <router-link class="btn_common" :to="{name: 'donate'}">Прокачать аккаунт!</router-link>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="chat_color col-12 col-lg-6 mt-5 mt-lg-0" :class="!hasPermission('prefix.text.edit') && !hasPermission('prefix.prefix_color.edit') && !hasPermission('prefix.nick_color.edit') && !hasPermission('prefix.msg_color.edit') ? 'locked' : ''">
            <div class="section">
                <h3>Настройки игрового чата</h3>
                <p>Управляйте визуализацией Вашего отображения в чате, установите уникальное оформление и станьте самым стильным на сервере!</p>
                <div class="body">
                    <div class="row align-items-center">
                        <label class="col-12 col-sm-6 col-xl-5">Выбор сервера</label>
                        <div class="col-12 col-sm">
                            <b-select class="btn_common select" :options="servers" value-field="id" text-field="name" v-model="prefix.server"></b-select>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <label class="col-12 col-sm-6 col-xl-5">Цвет префикса в чате</label>
                        <div class="col-12 col-sm">
                            <b-select class="btn_common select" id="prefix_color" :options="colors" value-field="char" text-field="name" v-model="prefix.prefix_color"></b-select>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <label class="col-12 col-sm-6 col-xl-5">Цвет Вашего ника в чате</label>
                        <div class="col-12 col-sm">
                            <b-select class="btn_common select" id="nick_color" :options="colors" value-field="char" text-field="name" v-model="prefix.nick_color"></b-select>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <label class="col-12 col-sm-6 col-xl-5">Цвет самого сообщения</label>
                        <div class="col-12 col-sm">
                            <b-select class="btn_common select" id="msg_color" :options="colors" value-field="char" text-field="name" v-model="prefix.msg_color"></b-select>
                        </div>
                    </div>
                    <div class="row align-items-center">
                        <label class="col-12 col-sm-6 col-xl-5">Слово в Вашем префиксе</label>
                        <div class="col-12 col-sm">
                            <b-input class="form-control-lg" :placeholder="roles[0].toUpperCase()" v-model="prefix.text" maxlength="10"></b-input>
                        </div>
                    </div>
                </div>
                <div class="footer" v-html="">
                    <span style="color: #555">[</span> <span :style="'color: ' + this.getColor(prefix.prefix_color).hex">{{ prefix.text }}</span> <span style="color: #555">]</span> <span :style="'color: ' + this.getColor(prefix.nick_color).hex">{{ user.login }}</span> <span style="color: #555">: </span> <span :style="'color: ' + this.getColor(prefix.msg_color).hex">Всем привет!</span>
                    <br><span style="color: #555">[</span> <span :style="'color: ' + this.getColor(prefix.prefix_color).hex">{{ prefix.text }}</span> <span style="color: #555">]</span> <span :style="'color: ' + this.getColor(prefix.nick_color).hex">{{ user.login }}</span> <span style="color: #555">: </span> <span :style="'color: ' + this.getColor(prefix.msg_color).hex">Как дела?</span>
                    <br><span style="color: #555">[</span> <span :style="'color: ' + this.getColor(prefix.prefix_color).hex">{{ prefix.text }}</span> <span style="color: #555">]</span> <span :style="'color: ' + this.getColor(prefix.nick_color).hex">{{ user.login }}</span> <span style="color: #555">: </span> <span :style="'color: ' + this.getColor(prefix.msg_color).hex">Я люблю DreamCMS!</span>
                    <br><span style="color: #555">[</span> <span :style="'color: ' + this.getColor(prefix.prefix_color).hex">{{ prefix.text }}</span> <span style="color: #555">]</span> <span :style="'color: ' + this.getColor(prefix.nick_color).hex">{{ user.login }}</span> <span style="color: #555">: </span> <span :style="'color: ' + this.getColor(prefix.msg_color).hex">Как вам мой новый цвет?</span>
                </div>
            </div>
            <div class="text-center mt-3" v-if="hasPermission('prefix.text.edit') || hasPermission('prefix.prefix_color.edit') || hasPermission('prefix.nick_color.edit') || hasPermission('prefix.msg_color.edit')">
                <a href="#" class="btn_common btn_common_lg" @click.prevent="savePrefix">Сохранить настройки префикса</a>
            </div>
            <div class="lock_screen">
                <i class="fal fa-lock-alt"></i>
                <p>Чтобы получить возможность устанавливать и настраивать Ваш личный префикс, Вам необходима привилегия Premium или выше.</p>
                <router-link class="btn_common btn_common_lg primary" :to="{name: 'donate'}">Прокачать аккаунт!</router-link>
            </div>
        </div>

        <input style="display: none;" id="skin_file" type="file" @change.prevent="uploadSkin($event)">
        <input style="display: none;" id="cloak_file" type="file" @change.prevent="uploadCloak($event)">
    </div>
</template>

<script>
    import api from "../api";
    import * as skinview3d from "skinview3d";
    import { mapGetters } from "vuex";
    import moment from "moment";
    import $ from "jquery";
    import { saveAs } from 'file-saver';

    export default {
        name: "CabinetIndex",
        computed: {
            ...mapGetters(['isLogged', 'user', 'role', 'servers', 'roles'])
        },
        data(){
            return {
                skinViewer: null,
                walk: false,
                rotate: false,
                run: false,
                flatSkin: false,

                skinLoading: false,

                colors: [
                    {char: '', name: 'Выберите...', disabled: true},
                    {char: '1', name: '[&1] Тёмно-синий', hex: '#0000AA'},
                    {char: '3', name: '[&3] Бирюзовый', hex: '#00AAAA'},
                    {char: '5', name: '[&5] Фиолетовый', hex: '#AA00AA'},
                    {char: '6', name: '[&6] Оранжевый', hex: '#FFAA00'},
                    {char: '7', name: '[&7] Серый', hex: '#aaa'},
                    {char: '8', name: '[&8] Тёмно-серый', hex: '#555'},
                    {char: '9', name: '[&9] Синий', hex: '#5555FF'},
                    {char: 'a', name: '[&a] Светло-зелёный', hex: '#55FF55'},
                    {char: 'b', name: '[&b] Голубой', hex: '#55FFFF'},
                    {char: 'c', name: '[&c] Красный', hex: '#FF5555'},
                    {char: 'd', name: '[&d] Розовый', hex: '#FF55FF'},
                    {char: 'e', name: '[&e] Жёлтый', hex: '#ffff55'},
                    {char: 'f', name: '[&f] Белый', hex: '#fff'},
                ],

                prefix: {
                    server: null,

                    text: '',
                    msg_color: '',
                    nick_color: '',
                    prefix_color: '',
                }
            }
        },
        mounted(){
            this.prefix.text = this.roles[0].toUpperCase();

            this.$nextTick(() => {
                this.initSkin();
            });
        },
        watch: {
            '$route': function () {
                this.$nextTick(() => {
                    this.initSkin();
                });
            },
            flatSkin: function (){
                this.$nextTick(() => {
                    this.initSkin();
                });
            },
            'prefix.text': function (newVal) {
                if (newVal.length > 10) {
                    this.prefix.text = newVal.substr(0, 10);
                }
            },
            'prefix.prefix_color': function (newVal) {
                $('#prefix_color').attr('style',
                    `background-color:${ this.getColor(newVal).hex } !important;`+
                    `background-image:${ this.getColor(newVal).img } !important;`+
                    `color:${ this.getColor(newVal).back } !important`
                );
            },
            'prefix.nick_color': function (newVal) {
                $('#nick_color').attr('style',
                    `background-color:${ this.getColor(newVal).hex } !important;`+
                    `background-image:${ this.getColor(newVal).img } !important;`+
                    `color:${ this.getColor(newVal).back } !important`
                );
            },
            'prefix.msg_color': function (newVal) {
                $('#msg_color').attr('style',
                    `background-color:${ this.getColor(newVal).hex } !important;`+
                    `background-image:${ this.getColor(newVal).img } !important;`+
                    `color:${ this.getColor(newVal).back } !important`
                );
            }
        },
        methods: {
            downloadFile(url, name){
                saveAs(url, name);
            },
            getColor(char){
                var find = this.colors.find(color => color.char === char);

                if(find){
                    var back = char === 'a' || char === 'b' || char === 'e' ? 'rgba(0, 0, 0, 0.6)' : (char === 'f') ? '' : '#fff';
                    var img = char === 'a' || char === 'b' || char === 'e' ? 'url("/assets/img/selectDropDark.svg")' : (char === 'f') ? '' : 'url("/assets/img/selectDropLight.svg")';

                    return {hex: find.hex, back: back, img: img};
                }
                return { hex: '', back: '', img: ''};
            },
            savePrefix(){
                api.post('profile/prefix/set', this.prefix).then(response => {
                    console.log(response);
                })
                .catch(error => {
                    console.log(error);
                });
            },
            uploadSkin(event){
                if (event.target.files) {
                    var formData = new FormData();
                    formData.append("type", 'skin');
                    formData.append("file", event.target.files[0]);
                    this.upload(formData);
                }
                else $('#skin_file').click();
            },
            uploadCloak(event){
                if (event.target.files) {
                    var formData = new FormData();
                    formData.append("type", 'cloak');
                    formData.append("file", event.target.files[0]);
                    this.upload(formData);
                }
                else $('#cloak_file').click();
            },
            updateSkin(){
                this.skinViewer.skinUrl = this.skinViewer.skinUrl + '?time=' + moment().unix();
                this.skinViewer.capeUrl = this.skinViewer.capeUrl + '?time=' + moment().unix();
            },
            upload(formData){
                this.skinLoading = true;

                api.post('skins/upload', formData, {headers: {'Content-Type': 'multipart/form-data'}})
                    .then(response => {
                        this.updateSkin();
                        this.skinLoading = false;
                    });
            },
            deleteSkin(){
                this.skinLoading = true;

                api.post('skins/delete', {
                    type: 'skin'
                }).then(response => {
                    this.updateSkin();
                    this.skinLoading = false;
                });
            },
            deleteCloak(){
                this.skinLoading = true;

                api.post('skins/delete', {
                    type: 'cloak'
                }).then(response => {
                    this.updateSkin();
                    this.skinLoading = false;
                });
            },
            initSkin(){
                if (!document.getElementById("skin_container")) return;

                this.skinViewer = new skinview3d.SkinViewer({
                    domElement: document.getElementById("skin_container"),
                    width: 300,
                    height: 320,
                    skinUrl: '/skins/' + this.user.uuid + '.png',
                    capeUrl: '/cloaks/' + this.user.uuid + '.png'
                });

                console.log("Init skin view");
                console.log(this.skinViewer);

                let control = skinview3d.createOrbitControls(this.skinViewer);
                control.enableRotate = true;
                control.enableZoom = false;
                control.enablePan = false;

                this.$set(this.skinViewer, 'animation', new skinview3d.CompositeAnimation());

                this.walk = this.skinViewer.animation.add(skinview3d.WalkingAnimation);
                this.rotate = this.skinViewer.animation.add(skinview3d.RotatingAnimation);
                this.run = this.skinViewer.animation.add(skinview3d.RunningAnimation);

                this.$set(this.run, 'paused', true);
                this.$set(this.rotate, 'speed', 3);
            },
        }
    }
</script>

<style scoped>

</style>