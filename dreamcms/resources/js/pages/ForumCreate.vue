<template>
    <div :class="'category ' + (this.loading ? 'unload' : '')">
        <div class="h2">
            <div class="row align-items-end text-center">
                <div class="col-12 col-md-6 mb-3 mb-md-0 text-md-left px-0 px-sm-3">
                    <h2>Создать новую тему</h2>
                    <p class="small">Будьте внимательные, за нарушение правил публикаций на форуме Вы можете получить блокировку!</p>
                </div>
            </div>
        </div>
        <div class="section create">
            <div class="row align-items-center">
                <div class="col-12 col-sm-4">
                    Введите заголовок
                    <small>Заголовок в списке тем (максимум 50 символов)</small>
                </div>
                <div class="col-12 col-sm col-md-5 col-xl-4">
                    <input class="form-control form-control-light mt-1 mt-sm-0" placeholder="Заголовок" v-model="title">
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12 col-sm-4">
                    Текст сообщения
                    <small>Максимум 1000 символов</small>
                </div>
                <div class="col-12 col-sm">
                    <editor id="newpost" :init="editor.init" v-model="content"></editor>
                </div>
            </div>
            <div class="row align-items-center mt-2">
                <div class="col-12 col-sm-4"></div>
                <div class="col text-center text-sm-left">
                    <a href="#" class="btn_common primary" @click.prevent="create">Создать новую тему</a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import api from "../api";

    import tinymce from 'tinymce/tinymce';
    import 'tinymce/themes/silver';

    import Editor from '@tinymce/tinymce-vue';

    export default {
        name: "ForumCreate",
        components: {
            Editor
        },
        data(){
            return {
                loading: false,
                title: '',
                content: '',

                editor:{
                    init:{
                        language: 'ru',
                        icons_url: '/assets/tinymce/icons.min.js',
                        plugins: 'spoiler preview paste autolink autosave code visualblocks visualchars image link media template codesample table hr toc insertdatetime advlist lists wordcount imagetools textpattern noneditable help charmap quickbars emoticons',
                        menubar: false,
                        toolbar: 'undo redo | bold italic underline strikethrough | formatselect fontsizeselect | alignleft aligncenter alignright alignjustify | outdent indent | numlist bullist | forecolor removeformat | spoiler-add spoiler-remove | emoticons | preview | image template link anchor codesample',
                        toolbar_sticky: true,
                        templates: [],
                    }
                }
            }
        },
        methods: {
            create(){
                this.loading = true;

                api.post('forum/discussion/create', {
                    title: this.title,
                    body: this.content,
                    category_slug: this.$route.params.category
                }).then(response => {
                    if (response.data.success){
                        this.$router.push({name: 'discussion', params: {slug: response.data.slug}});
                    }
                }).finally(() => {
                    this.loading = false;
                });
            }
        }
    }
</script>

<style scoped>

</style>