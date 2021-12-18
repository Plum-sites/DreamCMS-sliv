<template>
    <div class="profile" :class="!this.loaded ? 'unload' : ''">
        <div class="row section">
            <div class="col-12 col-xl-6">
                <h3>Опции профиля</h3>
                <p>Настраивайте Ваш форумный профиль под свои нужды, используйте все наши социальные функции и становитесь крайне значимой фигурой в игровом сообществе!</p>
                <p class="options">
                    <label>
                        <input type="checkbox" class="checkbox" v-model="show_signs">
                        Показывать подписи других игроков на форуме
                    </label>
                    <label>
                        <input type="checkbox" class="checkbox" v-model="hide_friends">
                        Скрыть друзей в профиле
                    </label>
                </p>
            </div>
            <div class="col-12 col-xl-6 mt-5 mt-xl-0">
                <h3>Сменить подпись</h3>
                <p>
                    <editor id="newpost" :init="editor.init" v-model="sign"></editor>

                    <a href="#" class="btn_common primary mt-2" @click.prevent="saveSign">Сохранить подпись</a>
                    <a href="#" class="btn_common mt-2" @click.prevent="clearSign">Очистить</a>
                </p>
            </div>
        </div>
    </div>
</template>

<script>
    import api from '../api';

    import tinymce from 'tinymce/tinymce';
    import 'tinymce/themes/silver';

    import Editor from '@tinymce/tinymce-vue';
    import $ from "jquery";
    import {mapGetters} from "vuex";

    export default {
        name: "CabinetProfile",
        components: { Editor },
        computed:{
            ...mapGetters(['user'])
        },
        mounted(){
            this.show_signs = this.user.show_signs;
            this.hide_friends = this.user.hide_friends;

            this.$nextTick(function () {
                this.load();
            });
        },
        data(){
            return {
                sign: '',

                loaded: false,

                show_signs: true,
                hide_friends: false,

                editor:{
                    init:{
                        language: 'ru',
                        icons_url: '/assets/tinymce/icons.min.js',
                        plugins: 'preview paste autolink autosave code visualblocks visualchars image link media template codesample table hr toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
                        menubar: false,
                        toolbar: 'undo redo | bold italic underline strikethrough | formatselect fontsizeselect | alignleft aligncenter alignright alignjustify | outdent indent |  numlist bullist | forecolor removeformat | emoticons | preview | image template link anchor codesample',
                        toolbar_sticky: true,
                        templates: [],
                    }
                },
            }
        },
        methods:{
            load(){
                api.get('/settings').then(response => {
                    this.sign = response.data.sign;
                    this.loaded = true;
                });
            },
            saveSign(){
                api.post('/settings/profile', {sign: this.sign});
            },
            clearSign(){
                this.sign = '';
                this.saveSign();
            }
        },
        watch:{
            show_signs: function (newVal) {
                if (this.loaded)
                    api.post('/settings/profile', {show_signs: newVal});
            },
            hide_friends: function (newVal) {
                if (this.loaded)
                    api.post('/settings/profile', {hide_friends: newVal});
            }
        }
    }
</script>

<style scoped>

</style>