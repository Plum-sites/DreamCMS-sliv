<template>
    <loader v-if="this.loading"></loader>
    <div class="news" v-else>
        <article class="news_item" v-for="article in news">
            <a href="#" class="row" @click="selected && selected.id === article.id ? selected = false : selected = article">
                <div :class="'col-12 ' + selected && selected.id === article.id ? 'col-xl-12' : 'col-xl-4'">
                    <div class="news_info">
                        <div class="news_meta">Новости</div>
                        <h3>{{ article.title }}</h3>
                        <p v-html="selected && selected.id === article.id ? article.full_content : article.short_content"></p>
                        <div class="news_date">{{ formatDate(article.created_at) }}</div>
                    </div>
                </div>
                <div :class="'col ' + (selected && selected.id === article.id ? 'ml-xl-0' : 'ml-xl-4')">
                    <div class="news_image">
                        <div class="img_wrapper" :style="'background-image:url(' + article.image + ')'"></div>
                    </div>
                </div>
            </a>
        </article>

        <div class="paging">
            <b-pagination v-model="pagination.current_page"
                          :total-rows="pagination.total"
                          :per-page="pagination.per_page">
            </b-pagination>
        </div>
    </div>
</template>

<script>
    import api from '../api'
    import moment from 'moment';
    import Loader from "../components/Loader";

    export default {
        name: "News",
        components: {Loader},
        mounted() {
            this.loadNews();
            this.checkRef();
        },
        data() {
            return {
                loading: true,
                news: [],
                selected: false,
                pagination: {
                    current_page: 1,
                    last_page: 1,
                    per_page: 10,
                    total: 1,
                }
            }
        },
        methods: {
            formatDate(date){
                moment.locale('ru');
                return moment(date).format('lll');
            },
            checkRef(){
                var ref = this.$route.query.ref;
                if (ref){
                    localStorage.setItem('refer', ref);
                }
            },
            loadNews() {
                this.loading = true;

                api.get('news/load', {params: {page: this.pagination.current_page}}).then(response => {
                    this.news = response.data.news.data;

                    this.pagination.current_page = response.data.news.current_page;
                    this.pagination.last_page = response.data.news.last_page;
                    this.pagination.per_page = response.data.news.per_page;
                    this.pagination.total = response.data.news.total;

                    this.loading = false;
                }).catch(error => {
                    console.error(error);
                });
            },
        },
        watch: {
            'pagination.current_page': function (val) {
                this.loadNews();
            }
        }
    }
</script>

<style scoped>

</style>